package ee.ut.hans.RuleML.service;

import ee.ut.hans.RuleML.Knowledgebase;
import ee.ut.hans.RuleML.RuleSet;
import ee.ut.hans.RuleML.service.messages.QueryRequest;
import ee.ut.hans.RuleML.service.messages.QueryResponse;
import ee.ut.hans.RuleML.service.messages.Request;
import ee.ut.hans.RuleML.service.messages.Response;
import javax.ejb.Stateless;
import javax.persistence.EntityManager;
import javax.persistence.PersistenceContext;
import javax.ws.rs.*;
import org.ruleml.oojdrew.COjDA;
import org.ruleml.oojdrew.SyntaxFormat;
import org.ruleml.oojdrew.parsing.RuleMLFormat;

/**
 *
 * @author Hans
 */
@Stateless
@Path("/RuleMLService")
public class KnowledgebaseFacadeREST extends AbstractFacade<Knowledgebase> {
	@PersistenceContext(unitName = "RuleMLAppPU")
	private EntityManager em;

	public KnowledgebaseFacadeREST() {
		super(Knowledgebase.class);
	}

	/**
	 * Create new ruleset
	 * 
	 * @param request
	 * @return 
	 */
	@POST
    @Consumes({"application/json"})
	@Produces({"application/json"})
	public Response createRuleset(Request request) {
		Knowledgebase entity = new Knowledgebase();
		entity.setId(super.count() + 1);
		entity.setRuleset(request.getRuleset());
		super.create(entity);
		return new Response(entity.getId());
	}
	
	/**
	 * Append new rules
	 * 
	 * @param request
	 * @return 
	 */
	@PUT
	@Path("append/{id}")
    @Consumes({"application/json"})
	public void append(@PathParam("id") Integer id, Request request) {
		Knowledgebase current = super.find(id);
		RuleSet currentRuleset = new RuleSet(current.getRuleset());
		RuleSet newRuleset = new RuleSet(request.getRuleset());
		currentRuleset.merge(newRuleset);
		current.setRuleset(currentRuleset.toString());
		super.edit(current);
	}

	/**
	 * Get ruleset
	 * 
	 * @param id
	 * @return 
	 */
	@GET
    @Path("{id}")
    @Produces({"application/json"})
	public Knowledgebase find(@PathParam("id") Integer id) {
		return super.find(id);
	}
	
	/**
	 * Query ruleset
	 * 
	 * @param id
	 * @param request
	 * @return 
	 */
	@POST
    @Path("query/{id}")
	@Consumes({"application/json"})
    @Produces({"application/json"})
	public QueryResponse query(@PathParam("id") Integer id, QueryRequest request) {
		Knowledgebase data = super.find(id);
		
		COjDA api = COjDA.getCOjDA();
		api.configureAPI(RuleMLFormat.RuleML100);
		
		String results = "";
		try {
			api.initializeKnowledgeBase(SyntaxFormat.RULEML, data.getRuleset());
			results = api.issueKnowledgebaseQuery(SyntaxFormat.RULEML, request.getQuery());
		} catch (Exception e) {
			results = e.getMessage();
		}
		
		return new QueryResponse(results);
	}

	@java.lang.Override
	protected EntityManager getEntityManager() {
		return em;
	}
	
}

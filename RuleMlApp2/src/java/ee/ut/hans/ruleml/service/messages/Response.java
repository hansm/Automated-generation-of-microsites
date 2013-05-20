package ee.ut.hans.ruleml.service.messages;

import java.io.Serializable;

/**
 * Simple response with just id field
 * 
 * @author Hans
 */
public class Response implements Serializable {
	
	private static final long serialVersionUID = 1L;
	
	/**
	 * Id for the ruleset
	 */
	private Integer id;

	private String ruleset;
	
	public Integer getId() {
		return this.id;
	}
	
	public void setId(Integer id) {
		this.id = id;
	}

	public String getRuleset() {
		return this.ruleset;
	}

	public void setRuleset(String ruleset) {
		this.ruleset = ruleset;
	}

	public Response() {}

	public Response(Integer id) {
		this.id = id;
	}

	public Response(String ruleset) {
		this.ruleset = ruleset;
	}

	public Response(Integer id, String ruleset) {
		this.id = id;
		this.ruleset = ruleset;
	}
	
}

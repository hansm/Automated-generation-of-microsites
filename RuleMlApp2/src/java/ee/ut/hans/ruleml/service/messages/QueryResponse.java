package ee.ut.hans.ruleml.service.messages;

import java.io.Serializable;

/**
 *
 * @author Hans
 */
public class QueryResponse implements Serializable {
	
	private static final long serialVersionUID = 1L;
	
	private String results;
	
	public void setResults(String results) {
		this.results = results;
	}
	
	public String getResults() {
		return this.results;
	}
	
	public QueryResponse(String results) {
		this.results = results;
	}
	
}

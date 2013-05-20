package ee.ut.hans.ruleml.service.messages;

import java.io.Serializable;

/**
 *
 * @author Hans
 */
public class QueryRequest implements Serializable {
	
	private static final long serialVersionUID = 1L;
	
	/**
	 * RuleML query
	 */
	private String query;
	
	public void setQuery(String query) {
		this.query = query;
	}
	
	public String getQuery() {
		return this.query;
	}
	
}
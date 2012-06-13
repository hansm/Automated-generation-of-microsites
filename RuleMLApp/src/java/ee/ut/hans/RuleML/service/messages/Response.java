package ee.ut.hans.RuleML.service.messages;

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
	
	public Integer getId() {
		return this.id;
	}
	
	public void setId(Integer id) {
		this.id = id;
	}
	
	public Response() { }

	public Response(Integer id) {
		this.id = id;
	}
	
}

package ee.ut.hans.ruleml.service.messages;

import java.io.Serializable;

/**
 *
 * @author Hans
 */
public class Request implements Serializable {
	
	private static final long serialVersionUID = 1L;
	
	private String ruleset;

	/**
	 * @return the mathml
	 */
	public String getRuleset() {
		return ruleset;
	}

	/**
	 * @param mathml the mathml to set
	 */
	public void setRuleset(String ruleset) {
		this.ruleset = ruleset;
	}
	
}

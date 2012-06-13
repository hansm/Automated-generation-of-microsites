package ee.ut.hans.RuleML;

import java.io.StringReader;
import java.io.StringWriter;
import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.transform.Result;
import javax.xml.transform.Transformer;
import javax.xml.transform.TransformerFactory;
import javax.xml.transform.dom.DOMSource;
import javax.xml.transform.stream.StreamResult;
import org.w3c.dom.Document;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;
import org.xml.sax.InputSource;

/**
 *
 * @author Hans
 */
public class RuleSet {
	
	/**
	 * Original RuleML rules string
	 */
	private String originalRuleMlString;
	
	/**
	 * DOM Document element
	 */
	private Document dom;
	
	/**
	 * Get RuleML document DOM element
	 * 
	 * @return RuleML ruleset DOM document
	 */
	public Document getDom() {
		return this.dom;
	}
	
	public RuleSet(String ruleMl) {
		this.originalRuleMlString = ruleMl;
		
		try {
			DocumentBuilder builder = DocumentBuilderFactory.newInstance().newDocumentBuilder();
			InputSource is = new InputSource(new StringReader(this.originalRuleMlString));
			dom = builder.parse(is);
		} catch (Exception e) {
		}
	}
	
	public RuleSet(Document document) {
		this.dom = document;
	}
	
	/**
	 * Merge other ruleset into this one
	 * 
	 * @param ruleSet 
	 */
	public void merge(RuleSet ruleSet) {
		Node thisDocumentElement = this.getDom().getDocumentElement();
		Node documentElement = ruleSet.getDom().getDocumentElement();
		NodeList children = documentElement.getChildNodes();
		for (int i = 0; i < children.getLength(); i++) {
			thisDocumentElement.appendChild(this.getDom().adoptNode(children.item(i).cloneNode(true)));
		}
	}
	
	@Override
	public String toString() {
		try {
            StringWriter stringWriter = new StringWriter();
            Result result = new StreamResult(stringWriter);
            TransformerFactory factory = TransformerFactory.newInstance();
            Transformer transformer = factory.newTransformer();
            transformer.transform(new DOMSource(this.getDom()), result);
            return stringWriter.getBuffer().toString();
        } catch (Exception e) {
			return "";
        }
	}
	
	public static void main(String[] args) {
		System.out.println("tere");
		
		RuleSet rulesetOne = new RuleSet("<?xml version=\"1.0\" encoding=\"UTF-8\"?>"
+"<?xml-model href=\"http://www.ruleml.org/1.0/relaxng/datalog_relaxed.rnc\"?>"
+"<RuleML xmlns=\"http://ruleml.org/spec\">"
+"	<Implies>"
+"		<if>"
+"			<Atom>"
+"				<Rel>equals</Rel>"
+"				<slot><Ind>category</Ind><Var>category</Var></slot>"
+"				<slot><Ind>category</Ind><Ind>menu</Ind></slot>"
+"			</Atom>"
+"		</if>"
+"		<then>"
+"			<Atom>"
+"				<Rel>location</Rel>"
+"				<slot><Ind>horizontal</Ind><Ind>right</Ind></slot>"
+"				<slot><Ind>veritical</Ind><Ind>top</Ind></slot>"
+"			</Atom>"
+"		</then>"
+"	</Implies>"
+"</RuleML>");
		
		System.out.println(rulesetOne.toString());
		
		RuleSet rulesetTwo = new RuleSet("<?xml version=\"1.0\" encoding=\"UTF-8\"?>"
+"<?xml-model href=\"http://www.ruleml.org/1.0/relaxng/datalog_relaxed.rnc\"?>"
+"<RuleML xmlns=\"http://ruleml.org/spec\">"
+"			<Atom>"
+"				<Rel>cool</Rel>"
+"				<slot><Ind>name</Ind><Ind>Hans</Ind></slot>"
+"			</Atom>"
+"</RuleML>");
		
		rulesetOne.merge(rulesetTwo);
		
		System.out.println(rulesetOne.toString());
	}
	
}

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package ee.ut.hans.ruleml;

import java.io.Serializable;
import javax.persistence.*;
import javax.validation.constraints.NotNull;
import javax.validation.constraints.Size;
import javax.xml.bind.annotation.XmlRootElement;

/**
 *
 * @author Hans
 */
@Entity
@Table(name = "Knowledgebase")
@XmlRootElement
@NamedQueries({
	@NamedQuery(name = "Knowledgebase.findAll", query = "SELECT k FROM Knowledgebase k"),
	@NamedQuery(name = "Knowledgebase.findById", query = "SELECT k FROM Knowledgebase k WHERE k.id = :id")})
public class Knowledgebase implements Serializable {
	private static final long serialVersionUID = 1L;
	@Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Basic(optional = false)
    @NotNull
    @Column(name = "id")
	private Integer id;
	@Lob
    @Size(max = 2147483647)
    @Column(name = "ruleset")
	private String ruleset;

	public Knowledgebase() {
	}

	public Knowledgebase(Integer id) {
		this.id = id;
	}

	public Integer getId() {
		return id;
	}

	public void setId(Integer id) {
		this.id = id;
	}

	public String getRuleset() {
		return ruleset;
	}

	public void setRuleset(String ruleset) {
		this.ruleset = ruleset;
	}

	@Override
	public int hashCode() {
		int hash = 0;
		hash += (id != null ? id.hashCode() : 0);
		return hash;
	}

	@Override
	public boolean equals(Object object) {
		// TODO: Warning - this method won't work in the case the id fields are not set
		if (!(object instanceof Knowledgebase)) {
			return false;
		}
		Knowledgebase other = (Knowledgebase) object;
		if ((this.id == null && other.id != null) || (this.id != null && !this.id.equals(other.id))) {
			return false;
		}
		return true;
	}

	@Override
	public String toString() {
		return "ee.ut.hans.ruleml.Knowledgebase[ id=" + id + " ]";
	}
	
}

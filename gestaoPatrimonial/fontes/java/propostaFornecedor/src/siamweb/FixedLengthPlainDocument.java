/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
/**
 * FixedLengthPlainDocument
 *
 * Classe que formata o documento utilizado e escreve nele
 *
 * Criado em 8 de Agosto de 2007, 09:57
 * Modificado em 23 de Janeiro de 2009 , 16:45 
 *
 *	Analista: Gelson W
 *	Desenvolvedor: Luiz Felipe P Teixeira
 *
   	$Id: $
 * 
 */

package urbem;

import javax.swing.text.AttributeSet;
import javax.swing.text.BadLocationException;
import javax.swing.text.PlainDocument;

/**
 * FixedLengthPlainDocument
 * 
 * Classe para formatar e editar documentos
 * 
 * @author luiz
 * @extends PlainDocument
 */
class FixedLengthPlainDocument extends PlainDocument 
{   
	private static final long serialVersionUID = 1L;
	private final int maxlength;

	/**
	 * FixedLengthPlainDocument
	 * 
	 * retorno o maximo de tamanho possivel para um documento   
	 * @param maxlength
	 */
	FixedLengthPlainDocument(final int maxlength) 
	{   
		this.maxlength = maxlength;   
	}

	/**
	 * 	insertString
	 * 
	 * Metodo que insere string em documento
	 */
	public void insertString(final int offset, final String str, final AttributeSet a) throws
		BadLocationException {   

		/**
		 * verifica o tamanho da informação passada mesmo sendo colada ou digitada
		 */
		if (!((getLength() + str.length()) > maxlength)) {   
			super.insertString(offset, str, a);   
		}
	}
}

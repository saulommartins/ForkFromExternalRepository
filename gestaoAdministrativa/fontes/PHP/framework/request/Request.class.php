<?php
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
?>
<?php
  /**
    * Classe para tratamento do $_REQUEST
    * Data de Criação   : 25/10/2013
    * @author Analista: Eduardo Schitz
    * @author Desenvolvedor: Carlos A. V. da Silva
    */
class Request
{
    private $array;

    /*
     * Método __construct recebe a superglobal $_REQUEST
     * Seta a propriedade array com todos os valores do parâmetro $request
     * Executa funções de proteção para evitar ataques XSS e SQL Inject
     */
    public function __construct($request)
    {
        foreach ($request as $key => $value) {
            if (!is_array($value)) {
                $value = addslashes($value);

                /*
                * No PHP 5.4 o charset default é UTF-8, essa função teve algumas mudanças:
                * 5.4.0	 The default value for the encoding parameter was changed to UTF-8.
                * 5.4.0	 The constants ENT_SUBSTITUTE, ENT_DISALLOWED, ENT_HTML401, ENT_XML1, ENT_XHTML and ENT_HTML5 were added.
                * 5.3.0	 The constant ENT_IGNORE was added.
                *
                * Logo o comportamento em relação ao PHP 5.3 é diferente.
                */

                // $value = htmlentities($value);
                // $value = htmlspecialchars($value);
            }

            $this->array[$key] = $value;
        }
    } // Fim do método __construct

    /*
     * Método get retorna dado setado no constructor.
     * Se o índice não existir, retorna o valor default
     * Se o valor default não existir, retorna null
     */
    public function get($key, $default=null)
    {
        if (isset($this->array[$key])) {
            return $this->array[$key];
        } else {
            if (is_null($default)) {
                return null;
            } else {
                return $default;
            }
        }
    } // Fim do método get

    /**
     * Retorna todos os elementos do formulário.
     *
     * @return array
     */
    public function getAll()
    {
        return $this->array;
    }

    /*
     * Método set
     * Seta variável no request se $key for string
     */
    public function set($key, $value)
    {
        if (is_string($key)) {
            $this->array[$key] = $value;

            return true;
        }

        return false;
    } // Fim do método set

} // Fim da class Request

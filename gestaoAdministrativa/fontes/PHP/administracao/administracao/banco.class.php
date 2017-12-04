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
* Manutenção de banco
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3149 $
$Name$
$Author: pablo $
$Date: 2005-11-30 13:54:33 -0200 (Qua, 30 Nov 2005) $

Casos de uso: uc-01.03.97
*/

class banco
{
    public $codBanco, $nomBanco;

/**************************************************************************
 Método construtor. Inicializa as variáveis de classe com valor nulo.
 Ou seta as variáveis, com o valor passado
***************************************************************************/
    public function banco($codBanco="", $nomBanco="")
    {
        $this->codBanco = $codBanco;
        $this->nomBanco = AddSlashes($nomBanco);

    }//Fim do método construtor

/**************************************************************************
 Inclui um novo Banco
***************************************************************************/
    public function incluirBanco()
    {
        $sql = "Insert Into administracao.banco (cod_banco,nom_banco)
                Values ('".$this->codBanco."', '".$this->nomBanco."'); ";
        //echo $sql; die();
        //Chama a classe do banco de dados e executa a query
        $conn = new dataBaseLegado;
        $conn->abreBD();
            if ($conn->executaSql($sql)) {
                $ok = true;
                //echo $sql;
            } else {
                $ok = false;
            }
        $conn->fechaBD();

        return $ok;
    }//Fim da function Incluir

/**************************************************************************
 Altera um Banco
***************************************************************************/
    public function alterarBanco()
    {
        $sql = "Update administracao.banco
                Set nom_banco = '".$this->nomBanco."'
                Where cod_banco = '".$this->codBanco."'; ";
        //Chama a classe do banco de dados e executa a query
        $conn = new dataBaseLegado;
        $conn->abreBD();
            if ($conn->executaSql($sql)) {
                $ok = true;
            } else {
                $ok = false;
            }
        $conn->fechaBD();

        return $ok;
    }//Fim da function alterar

/**************************************************************************
 Exclui um Banco
***************************************************************************/
    public function excluirBanco()
    {
        $sql = "Delete From administracao.banco
                Where cod_banco = '".$this->codBanco."'; ";
        //Chama a classe do banco de dados e executa a query
        $conn = new dataBaseLegado;
        $conn->abreBD();
            if ($conn->executaSql($sql)) {
                $ok = true;
            } else {
                $ok = false;
            }
        $conn->fechaBD();

        return $ok;
    }//Fim da function excluir

}//Fim da classe

?>

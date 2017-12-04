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
* Manutenção de agência
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3149 $
$Name$
$Author: pablo $
$Date: 2005-11-30 13:54:33 -0200 (Qua, 30 Nov 2005) $

Casos de uso: uc-01.03.97
*/

class agencia
{
    public $codBanco, $codAgencia, $nomAgencia, $nomBanco;

/**************************************************************************
 Método construtor. Inicializa as variáveis de classe com valor nulo.
 Ou seta as variáveis, com o valor passado
***************************************************************************/
    public function agencia($codBanco="", $codAgencia="", $nomAgencia="")
    {
        $this->codBanco = $codBanco;
        $this->codAgencia = $codAgencia;
        $this->nomAgencia = AddSlashes($nomAgencia);

    }//Fim do método construtor

/**************************************************************************
 Inclui um novo Agencia
***************************************************************************/
    public function incluirAgencia()
    {
        $sql = "Insert Into administracao.agencia (cod_banco,cod_agencia,nom_agencia)
                Values ('".$this->codBanco."', '".$this->codAgencia."', '".$this->nomAgencia."'); ";
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
    }//Fim da function Incluir

/**************************************************************************
 Altera um Agencia
***************************************************************************/
    public function alterarAgencia()
    {
        $sql = "Update administracao.agencia
                Set nom_agencia = '".$this->nomAgencia."'
                Where cod_banco = '".$this->codBanco."'
                And cod_agencia = '".$this->codAgencia."'; ";
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
 Exclui um Agencia
***************************************************************************/
    public function excluirAgencia()
    {
        $sql = "Delete From administracao.agencia
                Where cod_banco = '".$this->codBanco."'
                And cod_agencia = '".$this->codAgencia."'; ";
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

    public function retornaAgencia()
    {
        $sql = "Select nom_banco, nom_agencia
                From administracao.banco as b, administracao.agencia as a
                Where b.cod_banco = a.cod_banco
                And a.cod_banco = ".$this->codBanco."
                And cod_agencia = ".$this->codAgencia;
        //echo $sql;
        //Pega os dados encontrados em uma query
        $conn = new dataBaseLegado;
        $conn->abreBD();
        $conn->abreSelecao($sql);
        $conn->fechaBD();
        $conn->vaiPrimeiro();
            if ($conn->numeroDeLinhas>0) {
                $this->nomBanco = $conn->pegaCampo("nom_banco");
                $this->nomAgencia = $conn->pegaCampo("nom_agencia");
            } else {
                $this->nomBanco = "";
                $this->nomAgencia = "";
            }
        $conn->limpaSelecao();
    }//Fim da function retornaAgencia

}//Fim da classe

?>

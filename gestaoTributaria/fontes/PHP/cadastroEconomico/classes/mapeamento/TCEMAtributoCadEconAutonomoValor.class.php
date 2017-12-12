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
  * Classe de mapeamento da tabela ECONOMICO.ATRIBUTO_CAD_ECON_AUTONOMO_VALOR
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMAtributoCadEconAutonomoValor.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.02.01
*/

/*
$Log$
Revision 1.8  2006/09/15 12:08:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMAtributoCadastroEconomico.class.php");

/**
  * Efetua conexão com a tabela  ECONOMICO.ATRIBUTO_CAD_ECON_AUTONOMO_VALOR
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCEMAtributoCadEconAutonomoValor extends PersistenteAtributosValores
{
/**
    * Método Construtor
    * @access Private
*/
function TCEMAtributoCadEconAutonomoValor()
{
    parent::PersistenteAtributosValores();
    $this->setTabela('economico.atributo_cad_econ_autonomo_valor');
    //$this->setPersistenteAtributo( new TCEMAtributoCadastroEconomico );

    $this->setCampoCod('');
    $this->setComplementoChave('inscricao_economica,cod_atributo,cod_cadastro,timestamp','cod_modulo');

    $this->AddCampo('inscricao_economica','integer',true,'',true,true);
    $this->AddCampo('cod_atributo','integer',true,'',true,true);
    $this->AddCampo('cod_cadastro','integer',true,'',true,true);
    $this->AddCampo('timestamp','timestamp',false,'',true,false);
    $this->AddCampo('valor','varchar',true,'500',false,false);
    $this->AddCampo('cod_modulo','integer',true,'',true,true);

}

function RemoveAtributoAutonomo($boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRemoveAtributoAutonomo();
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaDML( $stSql, $boTransacao );

    return $obErro;
}

function montaRemoveAtributoAutonomo()
{
    $stSql  = "  DELETE FROM  economico.atributo_cad_econ_autonomo_valor \n";
    $stSql .= "  WHERE inscricao_economica = ".$this->getDado( "valor" )."\n";

    return $stSql;
}

}

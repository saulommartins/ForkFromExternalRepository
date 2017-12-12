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
* Classe de mapeamento
* Data de Criação: 06/07/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @package URBEM
* @subpackage Regra

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

* Casos de uso: uc-04.01.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TConcursoHomologacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TConcursoHomologacao()
{
    parent::Persistente();
    $this->setTabela('concurso.homologacao');

    $this->setCampoCod('cod_edital');
    $this->setComplementoChave('');

    $this->AddCampo('cod_edital','integer',true,'',true,false);
    $this->AddCampo('cod_homologacao','integer',true,'',false,true);
}

function recuperaRelacionamento(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRelacionamento().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamento()
{
    $stSql  = "SELECT                           \n";
    $stSql .= " ch.*,                           \n";
    $stSql .= " na.nom_norma,                   \n";
    $stSql .= " to_char(na.dt_publicacao, 'yyyy') as ano_publicacao, \n";
    $stSql .= " to_char(na.dt_publicacao, 'dd/mm/yyyy') as dt_publicacao \n";
    $stSql .= "FROM                             \n";
    $stSql .= " concurso.homologacao as ch, \n";
    $stSql .= " normas.norma as na                 \n";
    $stSql .= "WHERE                            \n";
    $stSql .= " ch.cod_homologacao = na.cod_norma\n";

    return $stSql;
}

}

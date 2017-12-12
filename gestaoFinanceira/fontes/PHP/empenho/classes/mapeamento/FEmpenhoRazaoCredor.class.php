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
    * Página de Formulario de Seleção de Impressora para Relatorio
    * Data de Criação   : 15/04/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @package URBEM
    * @subpackage Mapaeamento

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2007-12-04 12:08:26 -0200 (Ter, 04 Dez 2007) $

    * Casos de uso: uc-02.03.11
*/

/*
$Log$
Revision 1.7  2007/08/08 19:45:17  cako
Bug#9819#

Revision 1.6  2006/07/05 20:46:56  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FEmpenhoRazaoCredor extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FEmpenhoRazaoCredor()
{
    parent::Persistente();

    $this->setTabela('empenho.fn_empenho_razao_credor');

    $this->AddCampo('exercicio'     ,'varchar',false,''    ,false,false);
    $this->AddCampo('cgm'           ,'integer',false,''    ,false,false);
    $this->AddCampo('credor'        ,'varchar',false,''    ,false,false);
    $this->AddCampo('data'          ,'text',false,''       ,false,false);
    $this->AddCampo('entidade'      ,'integer',false,''    ,false,false);
    $this->AddCampo('empenho'       ,'integer',false,''    ,false,false);
    $this->AddCampo('despesa'       ,'varchar',false,''    ,false,false);
    $this->AddCampo('empenhado'     ,'numeric',false,'14.2',false,false);
    $this->AddCampo('anulado'       ,'numeric',false,'14.2',false,false);
    $this->AddCampo('liquidado'     ,'numeric',false,'14.2',false,false);
    $this->AddCampo('pago'          ,'numeric',false,'14.2',false,false);
}

function montaRecuperaTodos()
{
    $stSql  = "select * \n";
    $stSql .= "  from " . $this->getTabela() . "('" . $this->getDado("stEntidade") ."',             \n";
    $stSql .= "  '" . $this->getDado("exercicioEmpenho") . "','" . $this->getDado("inOrgao")."',         \n";
    $stSql .= "  '" . $this->getDado("inUnidade")."','" . $this->getDado("stElementoDespesa")."',   \n";
    $stSql .= "  '" . str_replace(".","",$this->getDado("stElementoDespesa"))."','" . $this->getDado("inRecurso")."', \n";
    $stSql .= "  '" . $this->getDado("stDestinacaoRecurso")."','" . $this->getDado("inCodDestalhamento")."',   \n";
    $stSql .= "  '" . $this->getDado("inCGM")."','".$this->getDado('exercicio')."') as retorno(                                        \n";
    $stSql .= "  exercicio           char(4),                                           \n";
    $stSql .= "  cgm                 integer,                                           \n";
    $stSql .= "  credor              varchar,                                           \n";
    $stSql .= "  data                text,                                              \n";
    $stSql .= "  entidade            integer,                                           \n";
    $stSql .= "  empenho             integer,                                           \n";
    $stSql .= "  despesa             varchar,                                           \n";
    $stSql .= "  empenhado           numeric,                                           \n";
    $stSql .= "  anulado             numeric,                                           \n";
    $stSql .= "  liquidado           numeric,                                           \n";
    $stSql .= "  pago                numeric                                            \n";
    $stSql .= "  )                                                                        ";

    return $stSql;
}

}

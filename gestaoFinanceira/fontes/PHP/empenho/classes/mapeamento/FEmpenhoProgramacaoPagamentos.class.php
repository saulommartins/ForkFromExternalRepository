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
    * Classe de mapeamento da tabela FN_EMPENHO_RELATORIO_PROGRAMACAO_PAGAMENTOS
    * Data de Criação: 15/08/2005

    * @author Analista: Muriel Preuss
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: cako $
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso : uc-02.03.26
*/

/*
$Log$
Revision 1.6  2006/07/05 20:46:56  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FEmpenhoProgramacaoPagamentos extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FEmpenhoProgramacaoPagamentos()
{
    parent::Persistente();
    $this->setTabela('empenho.fn_relatorio_programacao_pagamentos');

    $this->AddCampo('cod_entidade'  ,'integer',false,''    ,false,false);
    $this->AddCampo('cod_empenho'   ,'integer',false,''    ,false,false);
    $this->AddCampo('exercicio'     ,'varchar',false,''    ,false,false);
    $this->AddCampo('dt_vencimento' ,'text',false,''       ,false,false);
    $this->AddCampo('cgm'           ,'integer',false,''    ,false,false);
    $this->AddCampo('credor'        ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_recurso'   ,'integer',false,''    ,false,false);
    $this->AddCampo('nom_recurso'   ,'varchar',false,''    ,false,false);
    $this->AddCampo('apagar'        ,'numeric',false,'14.2',false,false);

}

function montaRecuperaTodos()
{
    $stSql  = "SELECT *                                                                                     \n";
    $stSql .= "  from " . $this->getTabela() . "(                                                           \n";
    $stSql .= "  '" . $this->getDado("stFiltro") . "','" . $this->getDado("stEntidade")."',                 \n";
    $stSql .= "  '" . $this->getDado("exercicio") . "','" . $this->getDado("stDataInicial")."',             \n";
    $stSql .= "  '" . $this->getDado("stDataFinal")."','" . $this->getDado("inCodFornecedor")."',           \n";
    $stSql .= "  '" . $this->getDado("inCodDespesa")."','" . $this->getDado("inCodRecurso")."',             \n";
    $stSql .= "  '" . $this->getDado("stDestinacaoRecurso")."','" . $this->getDado("inCodDetalhamento")."'  \n";
    $stSql .= "  ) as retorno(                                                                              \n";
    $stSql .= "    cod_entidade         integer,                                                            \n";
    $stSql .= "    cod_empenho          integer,                                                            \n";
    $stSql .= "    exercicio            char(4),                                                            \n";
    $stSql .= "    dt_vencimento        text,                                                               \n";
    $stSql .= "    cgm                  integer,                                                            \n";
    $stSql .= "    credor               varchar,                                                            \n";
    $stSql .= "    cod_recurso          varchar,                                                            \n";
    $stSql .= "    nom_recurso          varchar,                                                            \n";
    $stSql .= "    apagar               numeric)                                                            \n";
    $stSql .= "  ORDER BY                                                                                   \n";
    $stSql .= "    dt_vencimento,                                                                           \n";
    $stSql .= "    cod_recurso,                                                                             \n";
    $stSql .= "    nom_recurso,                                                                             \n";
    $stSql .= "    cod_entidade,                                                                            \n";
    $stSql .= "    exercicio,                                                                               \n";
    $stSql .= "    cod_entidade,                                                                            \n";
    $stSql .= "    cod_empenho,                                                                             \n";
    $stSql .= "    cgm,                                                                                     \n";
    $stSql .= "    credor                                                                                   \n";

    return $stSql;
}

}
?>

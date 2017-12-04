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
    * Classe de mapeamento da tabela FN_EMPENHO_EMPENHO_PAGAR
    * Data de Criação: 21/02/2005

    * @author Analista: Muriel Preuss
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @package URBEM
    * @subpackage Mapeamento

    * $Id: FEmpenhoEmpenhoPagar.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso : uc-02.03.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class FEmpenhoEmpenhoPagar extends Persistente
{

    /**
        * Método Construtor
        * @access Private
    */
    public function FEmpenhoEmpenhoPagar()
    {
        parent::Persistente();

        $this->setTabela('empenho.fn_relatorio_empenhos_a_pagar');

        $this->AddCampo('cod_entidade'  ,'integer',false,''    ,false,false);
        $this->AddCampo('cod_empenho'   ,'integer',false,''    ,false,false);
        $this->AddCampo('exercicio'     ,'varchar',false,''    ,false,false);
        $this->AddCampo('dt_emissao'    ,'text',false,''       ,false,false);
        $this->AddCampo('cgm'           ,'integer',false,''    ,false,false);
        $this->AddCampo('credor'        ,'varchar',false,''    ,false,false);
        $this->AddCampo('empenhado'     ,'numeric',false,'14.2',false,false);
        $this->AddCampo('liquidado'     ,'numeric',false,'14.2',false,false);
        $this->AddCampo('pago'          ,'numeric',false,'14.2',false,false);
        $this->AddCampo('apagar'        ,'numeric',false,'14.2',false,false);
        $this->AddCampo('apagarliquidado','numeric',false,'14.2',false,false);

    }

    public function montaRecuperaTodos()
    {
        $stSql  = "SELECT *                                                                                     \n";
        $stSql .= "  from " . $this->getTabela() . "(                                                           \n";
        $stSql .= "  '" . $this->getDado("stFiltro") . "','" . $this->getDado("stEntidade")."',                 \n";
        $stSql .= "  '" . $this->getDado("exercicio") . "','" . $this->getDado("stDataInicial")."',             \n";
        $stSql .= "  '" . $this->getDado("stDataFinal")."','" . $this->getDado("stDataSituacao")."',            \n";
        $stSql .= "  '" . $this->getDado("inCodEmpenhoInicial")."','" . $this->getDado("inCodEmpenhoFinal")."', \n";
        $stSql .= "  '" . $this->getDado("inCodFornecedor")."','" . $this->getDado("inNumOrgao")."','".$this->getDado("inOrdenacao")."'            \n";
        $stSql .= "  ) as retorno(                                                                              \n";
        $stSql .= "    cod_entidade         integer,                                                            \n";
        $stSql .= "    cod_empenho          integer,                                                            \n";
        $stSql .= "    exercicio            char(4),                                                            \n";
        $stSql .= "    dt_emissao           text,                                                               \n";
        $stSql .= "    cgm                  integer,                                                            \n";
        $stSql .= "    credor               varchar,                                                            \n";
        $stSql .= "    empenhado            numeric,                                                            \n";
        $stSql .= "    liquidado            numeric,                                                            \n";
        $stSql .= "    pago                 numeric,                                                            \n";
        $stSql .= "    apagar               numeric,                                                            \n";
        $stSql .= "    apagarliquidado      numeric,                                                            \n";
        $stSql .= "    cod_recurso          integer,                                                            \n";
        $stSql .= "    nom_recurso          varchar,                                                            \n";
        $stSql .= "    masc_recurso_red     varchar)                                                            \n";

        return $stSql;
    }

}
?>

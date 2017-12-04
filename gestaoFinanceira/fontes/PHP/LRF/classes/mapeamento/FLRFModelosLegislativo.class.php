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
    * Classe de Mapeamento
    * Data de Criação: 25/05/2005

    * @author Analista: Diego Barbosa
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.05.11
*/

/*
$Log$
Revision 1.7  2006/07/05 20:44:36  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FLRFModelosLegislativo extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FLRFModelosLegislativo()
{
    parent::Persistente();

    $this->setTabela('tcers.fn_rel_modelos_legislativo');

    $this->AddCampo('cod_modelo'        ,'integer',false,''    ,false,false);
    $this->AddCampo('nom_modelo'        ,'varchar',false,''    ,false,false);
    $this->AddCampo('exercicio'         ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_quadro'        ,'integer',false,''    ,false,false);
    $this->AddCampo('nom_quadro'        ,'varchar',false,''    ,false,false);
    $this->AddCampo('redutora'          ,'boolean',false,''    ,false,false);
    $this->AddCampo('nom_conta'         ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_estrutural_c'  ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_estrutural'    ,'varchar',false,''    ,false,false);
    $this->AddCampo('vl_contabil'       ,'numeric',false,'14.2',false,false);
    $this->AddCampo('vl_ajuste'         ,'numeric',false,'14.2',false,false);
}

function montaRecuperaTodos()
{
    $stSql  = "select * \n";
    $stSql .= "  from " . $this->getTabela() . "('" . $this->getDado("exercicio") ."',             \n";
    $stSql .= "  '" . $this->getDado("stDataInicial") . "','" . $this->getDado("stDataFinal")."',         \n";
    $stSql .= "  '" . $this->getDado("stEntidade")."','" . $this->getDado("inCodModelo")."',   \n";
    $stSql .= "  '" . $this->getDado("stTipoValorDespesa")."','" . $this->getDado("stFiltro"). "' \n";
    $stSql .= "  ) as retorno(                                        \n";
    $stSql .= "  cod_modelo          integer,                                           \n";
    $stSql .= "  nom_modelo          varchar,                                           \n";
 // $stSql .= "  exercicio           char(4),                                           \n";
    $stSql .= "  cod_quadro          integer,                                           \n";
    $stSql .= "  nom_quadro          varchar,                                           \n";
    $stSql .= "  redutora            boolean,                                           \n";
    $stSql .= "  ordem               integer,                                           \n";
    $stSql .= "  nom_conta           varchar,                                           \n";
    $stSql .= "  cod_estrutural_c    varchar,                                           \n";
    $stSql .= "  cod_estrutural      varchar,                                           \n";
    $stSql .= "  vl_contabil         numeric,                                           \n";
    $stSql .= "  vl_ajuste           numeric,                                           \n";
    $stSql .= "  vl_ajustado         numeric                                            \n";
    $stSql .= "  )                                                                        ";

    return $stSql;
}

}

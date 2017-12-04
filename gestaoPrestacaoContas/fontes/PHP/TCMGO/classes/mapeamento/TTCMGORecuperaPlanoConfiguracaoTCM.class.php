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
    * Classe de mapeamento da tabela
    * Data de Criação: 12/05/2014

    * @author Desenvolvedor: Eduardo Paculski Schitz

    * @package URBEM
    * @subpackage Mapeamento

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );
class TTCMGORecuperaPlanoConfiguracaoTCM extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTCMGORecuperaPlanoConfiguracaoTCM()
{
    parent::Persistente();
    $this->setTabela('tcmgo.recupera_plano_configuracao_tcm');

    $this->AddCampo('cod_conta'           ,'integer',false,''    ,false,false);
    $this->AddCampo('cod_estrutural'      ,'varchar',false,''    ,false,false);
    $this->AddCampo('exercicio'           ,'char'   ,false,'4'   ,false,false);
    $this->AddCampo('cod_plano_tcmgo'     ,'integer',false,''    ,false,false);
    $this->AddCampo('nom_conta'           ,'varchar',false,''    ,false,false);
    $this->AddCampo('obrigatorio_tcmgo'   ,'boolean', true,''    ,false,false);
    $this->AddCampo('vl_saldo_anterior'   ,'numeric',false,'14.2',false,false);
    $this->AddCampo('vl_saldo_debitos'    ,'numeric',false,'14.2',false,false);
    $this->AddCampo('vl_saldo_creditos'   ,'numeric',false,'14.2',false,false);
    $this->AddCampo('vl_saldo_atual'      ,'numeric',false,'14.2',false,false);
}

function montaRecuperaTodos()
{
    $stSql  = " select * , CASE WHEN obrigatorio_tcmgo IS TRUE THEN '*' ELSE '' END AS desc_obrigatorio ";
    $stSql .= "   from ".$this->getTabela()."('".$this->getDado("exercicio")."',     \n";
    $stSql .= "                                      '".$this->getDado("entidades")."',     \n";
    $stSql .= "                                      '".$this->getDado("data_inicial")."',  \n";
    $stSql .= "                                      '".$this->getDado("data_final")."',    \n";
    $stSql .= "                                      '".$this->getDado("grupo")."'  ) AS    \n";
    $stSql .= "retorno (cod_conta           INTEGER,  \n";
    $stSql .= "         cod_plano           INTEGER,  \n";
    $stSql .= "         cod_estrutural      VARCHAR,  \n";
    $stSql .= "         exercicio           CHAR(4),  \n";
    $stSql .= "         cod_plano_tcmgo     INTEGER,  \n";
    $stSql .= "         nom_conta           VARCHAR,  \n";
    $stSql .= "         obrigatorio_tcmgo   BOOLEAN,  \n";
    $stSql .= "         vl_saldo_anterior   NUMERIC,  \n";
    $stSql .= "         vl_saldo_debitos    NUMERIC,  \n";
    $stSql .= "         vl_saldo_creditos   NUMERIC,  \n";
    $stSql .= "         vl_saldo_atual      NUMERIC)  \n";

    return $stSql;
}

}

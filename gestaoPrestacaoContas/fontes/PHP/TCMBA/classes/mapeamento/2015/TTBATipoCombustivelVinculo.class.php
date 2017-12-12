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
    * Data de Criação: 20/08/2008

    * @author Analista      : Tonismar Régis Bernardo
    * @author Desenvolvedor : Henrique Boaventura

    * @ignore

    * $Id: TTBATipoCombustivelVinculo.class.php 62823 2015-06-24 17:22:01Z evandro $

    * Casos de uso: uc-06.05.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTBATipoCombustivelVinculo extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTBATipoCombustivelVinculo()
{
    parent::Persistente();
    $this->setTabela('tcmba.tipo_combustivel_vinculo');

    $this->setCampoCod('cod_tipo_tcm');
    $this->setComplementoChave('cod_combustivel');

    $this->AddCampo('cod_tipo_tcm','integer',true,'',true,'TTBATipoVeiculo');
    $this->AddCampo('cod_combustivel','integer',true,'',true,'TFrotaCombustivel');
}

function recuperaTipoCombustivelVinculo(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaTipoCombustivelVinculo",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}
function montaRecuperaTipoCombustivelVinculo()
{
    $stSql = "
        SELECT tipo_combustivel.cod_tipo_tcm
             , tipo_combustivel.descricao AS nom_tipo_tcm
             , combustivel.cod_combustivel AS cod_tipo_sw
             , combustivel.nom_combustivel AS nom_tipo_sw
          FROM tcmba.tipo_combustivel_vinculo
    INNER JOIN tcmba.tipo_combustivel
            ON tipo_combustivel.cod_tipo_tcm = tipo_combustivel_vinculo.cod_tipo_tcm
    INNER JOIN frota.combustivel
            ON combustivel.cod_combustivel = tipo_combustivel_vinculo.cod_combustivel
    ";

    return $stSql;

}

}
?>

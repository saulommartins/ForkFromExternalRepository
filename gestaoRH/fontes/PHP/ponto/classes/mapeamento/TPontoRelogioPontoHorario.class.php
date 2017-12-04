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
    * Classe de mapeamento da tabela ponto.relogio_ponto_horario
    * Data de Criação: 21/10/2008

    * @author Analista     : Dagiane Vieira
    * @author Desenvolvedor: Rafael Garbin

    * @package URBEM
    * @subpackage Mapeamento

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPontoRelogioPontoHorario extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPontoRelogioPontoHorario()
{
    parent::Persistente();
    $this->setTabela("ponto.relogio_ponto_horario");

    $this->setCampoCod('cod_horario');
    $this->setComplementoChave('cod_contrato,timestamp,cod_ponto');

    $this->AddCampo('cod_contrato','integer'  ,true  ,'',true,'TPontoRelogioPontoDias');
    $this->AddCampo('cod_ponto'   ,'integer'  ,true  ,'',true,'TPontoRelogioPontoDias');
    $this->AddCampo('timestamp'   ,'timestamp_now',true  ,'',false);
    $this->AddCampo('cod_horario' ,'sequence' ,true  ,'',true,false);
    $this->AddCampo('hora'        ,'time'     ,true  ,'',false,false);

}

function montaRecuperaTodosHorariosDia()
{
    $stSql  = "    SELECT relogio_ponto_horario.*                                                \n";
    $stSql .= "      FROM ponto.relogio_ponto_horario                                            \n";
    $stSql .= "INNER JOIN ponto.relogio_ponto_dias                                               \n";
    $stSql .= "        ON relogio_ponto_horario.cod_contrato = relogio_ponto_dias.cod_contrato   \n";
    $stSql .= "       AND relogio_ponto_horario.cod_ponto = relogio_ponto_dias.cod_ponto         \n";

    return $stSql;
}

function recuperaTodosHorariosDia(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaTodosHorariosDia",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}
}
?>

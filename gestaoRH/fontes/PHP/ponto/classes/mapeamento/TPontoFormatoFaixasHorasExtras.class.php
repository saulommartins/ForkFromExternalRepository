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
    * Classe de mapeamento da tabela ponto.formato_faixas_horas_extras
    * Data de Criação: 21/10/2008

    * @author Analista     : Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPontoFormatoFaixasHorasExtras extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPontoFormatoFaixasHorasExtras()
{
    parent::Persistente();
    $this->setTabela("ponto.formato_faixas_horas_extras");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_formato,cod_dado,cod_configuracao,timestamp,cod_faixa');

    $this->AddCampo('cod_formato'     ,'integer'  ,true  ,'',true,'TPontoDadosExportacao');
    $this->AddCampo('cod_dado'        ,'integer'  ,true  ,'',true,'TPontoDadosExportacao');
    $this->AddCampo('cod_configuracao','integer'  ,true  ,'',true,'TPontoFaixasHorasExtra');
    $this->AddCampo('timestamp'       ,'timestamp',true  ,'',true,'TPontoFaixasHorasExtra');
    $this->AddCampo('cod_faixa'       ,'integer'  ,true  ,'',true,'TPontoFaixasHorasExtra');

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT formato_faixas_horas_extras.*\n";
    $stSql .= "  FROM ponto.formato_faixas_horas_extras\n";
    $stSql .= "  JOIN ponto.configuracao_relogio_ponto\n";
    $stSql .= "    ON configuracao_relogio_ponto.cod_configuracao = formato_faixas_horas_extras.cod_configuracao\n";
    $stSql .= "   AND configuracao_relogio_ponto.ultimo_timestamp = formato_faixas_horas_extras.timestamp\n";
    $stSql .= " WHERE NOT EXISTS (SELECT 1\n";
    $stSql .= "                     FROM ponto.configuracao_relogio_ponto_exclusao\n";
    $stSql .= "                    WHERE configuracao_relogio_ponto_exclusao.cod_configuracao = configuracao_relogio_ponto.cod_configuracao)\n";

    return $stSql;
}

}
?>

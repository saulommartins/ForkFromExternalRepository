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
    * Classe de mapeamento da tabela ponto.configuracao_parametros_gerais
    * Data de Criação: 14/10/2008

    * @author Analista     : Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPontoConfiguracaoParametrosGerais extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPontoConfiguracaoParametrosGerais()
{
    parent::Persistente();
    $this->setTabela("ponto.configuracao_parametros_gerais");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_configuracao,timestamp');

    $this->AddCampo('cod_configuracao' ,'integer'      ,true  ,''     ,true,'TPontoConfiguracaoRelogioPonto');
    $this->AddCampo('timestamp'        ,'timestamp_now',true  ,''     ,true,false);
    $this->AddCampo('cod_dia_dsr'      ,'integer'      ,true  ,''     ,false,'TAdministracaoDiasSemana','cod_dia');
    $this->AddCampo('descricao'        ,'varchar'      ,true  ,'100'  ,false,false);
    $this->AddCampo('limitar_atrasos'  ,'boolean'      ,true  ,''     ,false,false);
    $this->AddCampo('hora_noturno1'    ,'time'         ,true  ,''     ,false,false);
    $this->AddCampo('hora_noturno2'    ,'time'         ,true  ,''     ,false,false);
    $this->AddCampo('separar_adicional','boolean'      ,true  ,''     ,false,false);
    $this->AddCampo('lancar_abono'     ,'boolean'      ,true  ,''     ,false,false);
    $this->AddCampo('lancar_desconto'  ,'boolean'      ,true  ,''     ,false,false);
    $this->AddCampo('trabalho_feriado' ,'boolean'      ,true  ,''     ,false,false);
    $this->AddCampo('somar_extras'     ,'boolean'      ,true  ,''     ,false,false);
    $this->AddCampo('vigencia'         ,'date'         ,true  ,''     ,false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql .= " SELECT configuracao_parametros_gerais.*                                                                                          \n";
    $stSql .= "      , to_char(vigencia,'DD/MM/YYYY') as vigencia_formatada                                                                      \n";
    $stSql .= "      , to_char(hora_noturno1,'HH24:MI') as hora_noturno1_formatada                                                               \n";
    $stSql .= "      , to_char(hora_noturno2,'HH24:MI') as hora_noturno2_formatada                                                               \n";
    $stSql .= "  FROM ponto.configuracao_parametros_gerais                                                              \n";
    $stSql .= "INNER JOIN ponto.configuracao_relogio_ponto                                                              \n";
    $stSql .= "        ON configuracao_relogio_ponto.cod_configuracao = configuracao_parametros_gerais.cod_configuracao                          \n";
    $stSql .= "       AND configuracao_relogio_ponto.ultimo_timestamp = configuracao_parametros_gerais.timestamp                                 \n";
    $stSql .= "       AND NOT EXISTS (SELECT 1                                                                                                   \n";
    $stSql .= "                         FROM ponto.configuracao_relogio_ponto_exclusao                                  \n";
    $stSql .= "                        WHERE configuracao_relogio_ponto_exclusao.cod_configuracao = configuracao_relogio_ponto.cod_configuracao) \n";

    return $stSql;
}

}
?>

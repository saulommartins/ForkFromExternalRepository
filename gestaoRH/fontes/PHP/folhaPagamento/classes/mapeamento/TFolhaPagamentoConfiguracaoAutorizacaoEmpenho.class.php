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
    * Classe de mapeamento da tabela folhapagamento.configuracao_autorizacao_empenho
    * Data de Criação: 13/07/2007

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-07-17 10:02:38 -0300 (Ter, 17 Jul 2007) $

    * Casos de uso: uc-04.05.29
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  folhapagamento.configuracao_autorizacao_empenho
  * Data de Criação: 13/07/2007

  * @author Analista: Dagiane Vieira
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoConfiguracaoAutorizacaoEmpenho extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoConfiguracaoAutorizacaoEmpenho()
{
    parent::Persistente();
    $this->setTabela("folhapagamento.configuracao_autorizacao_empenho");

    $this->setCampoCod('cod_configuracao_autorizacao');
    $this->setComplementoChave('exercicio,vigencia,timestamp');

    $this->AddCampo('cod_configuracao_autorizacao','sequence'      ,true  ,''   ,true,false);
    $this->AddCampo('exercicio'                   ,'char'          ,true  ,'4'  ,true,false);
    $this->AddCampo('cod_modalidade'              ,'integer'       ,true  ,''   ,false,'TComprasModalidade');
    $this->AddCampo('numcgm'                      ,'integer'       ,true  ,''   ,false,'TCGMCGM');
    $this->AddCampo('complementar'                ,'boolean'       ,true  ,''   ,false,false);
    $this->AddCampo('descricao_item'              ,'varchar'       ,true  ,'160',false,false);
    $this->AddCampo('vigencia'                    ,'date'          ,true  ,''   ,false,false);
    $this->AddCampo('timestamp'                   ,'timestamp_now' ,true  ,''   ,true,false);

}

function montaRecuperaRelacionamento()
{
    $stSql  = "    SELECT configuracao_autorizacao_empenho.*
                     FROM folhapagamento.configuracao_autorizacao_empenho
               INNER JOIN (   SELECT exercicio
                                   , vigencia
                                   , max(timestamp) as timestamp
                                FROM folhapagamento.configuracao_autorizacao_empenho
                            GROUP BY exercicio
                                   , vigencia
                        ) as max_configuracao_autorizacao_empenho
                       ON configuracao_autorizacao_empenho.exercicio                    = max_configuracao_autorizacao_empenho.exercicio
                      AND configuracao_autorizacao_empenho.timestamp                    = max_configuracao_autorizacao_empenho.timestamp
                      AND configuracao_autorizacao_empenho.vigencia                    = max_configuracao_autorizacao_empenho.vigencia";

    return $stSql;
}

function recuperaConfiguracaoAutorizacaoEmpenho(&$rsRecordSet, $stFiltro="", $stOrdem="")
{
    $obErro = $this->executaRecupera("montaRecuperaConfiguracaoAutorizacaoEmpenho",$rsRecordSet,$stFiltro,$stOrdem);

    return $obErro;
}

function montaRecuperaConfiguracaoAutorizacaoEmpenho()
{
    $stSql = "
         SELECT cod_configuracao_autorizacao 
              , exercicio
              , cod_modalidade
              , numcgm
              , complementar
              , descricao_item
              , TO_CHAR(vigencia,'dd/mm/yyyy') AS vigencia
              , TO_CHAR(timestamp,'yyyy-mm-dd hh24:mi:ss.us') AS timestamp 
           FROM folhapagamento.configuracao_autorizacao_empenho 
          WHERE cod_configuracao_autorizacao = ".$this->getDado('cod_configuracao_autorizacao')."
            AND exercicio = '".$this->getDado('exercicio')."' 
            AND TO_DATE(vigencia::VARCHAR, 'dd/mm/yyyy') = TO_DATE('".$this->getDado('vigencia')."','dd/mm/yyyy') 
            AND timestamp = TO_TIMESTAMP('".$this->getDado('timestamp')."','yyyy-mm-dd hh24:mi:ss.us')
    ";

    return $stSql;
}

}
?>

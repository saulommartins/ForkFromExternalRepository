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
    * Classe de mapeamento da tabela FOLHAPAGAMENTO.EVENTO_CALCULADO
    * Data de Criação: 23/04/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: rgarbin $
    $Date: 2008-04-10 11:10:23 -0300 (Qui, 10 Abr 2008) $

    * Casos de uso: uc-04.05.09
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  FOLHAPAGAMENTO.EVENTO_CALCULADO
  * Data de Criação: 05/12/2005

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoEventoCalculadoDependente extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoEventoCalculadoDependente()
{
    parent::Persistente();
    $this->setTabela('folhapagamento.evento_calculado_dependente');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_evento,cod_registro,timestamp_registro,cod_dependente');

    $this->AddCampo('cod_evento','integer',true,'',true             ,'TFolhaPagamentoEventoCalculado');
    $this->AddCampo('cod_registro','integer',true,'',true           ,'TFolhaPagamentoEventoCalculado');
    $this->AddCampo('timestamp_registro','timestamp',true,'',true   ,'TFolhaPagamentoEventoCalculado');
    $this->AddCampo('cod_dependente','integer',true,'',true         ,'TPessoalDependente');
    $this->AddCampo('valor','numeric',true,'15,2',false,false);
    $this->AddCampo('quantidade','numeric',true,'15,2',false,false);
    $this->AddCampo('timestamp','timestamp',false,'',false,false);

}

function recuperaContratosCalculadosRemessaBancos(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    if (trim($stOrdem)=="") {$stOrdem="ORDER BY nom_cgm";}
    $obErro = $this->executaRecupera("montaRecuperaContratosCalculadosRemessaBancos",$rsRecordSet,$stFiltro,$stOrdem);

    return $obErro;
}

function montaRecuperaContratosCalculadosRemessaBancos()
{
    $stSqlDesdobramento = "";
    if ($this->getDado("stDesdobramento")!="") {
        $stSqlDesdobramento = "\n WHERE desdobramento = '".$this->getDado("stDesdobramento")."'";
    }

    $stSql .= "\n           SELECT *";
    $stSql .= "\n             FROM (";
    $stSql .= "\n              SELECT dependentes_calculados.cod_contrato";
    $stSql .= "\n                   , CASE WHEN responsavel_legal.numcgm IS NULL THEN";
    $stSql .= "\n                       (SELECT nom_cgm FROM sw_cgm WHERE numcgm = dependente.numcgm)";
    $stSql .= "\n                     ELSE";
    $stSql .= "\n                       (SELECT nom_cgm FROM sw_cgm WHERE numcgm = responsavel_legal.numcgm)";
    $stSql .= "\n                     END as nom_cgm";
    $stSql .= "\n                   , CASE WHEN responsavel_legal.numcgm IS NULL THEN";
    $stSql .= "\n                       (SELECT cpf FROM sw_cgm_pessoa_fisica WHERE numcgm = dependente.numcgm)";
    $stSql .= "\n                     ELSE";
    $stSql .= "\n                       (SELECT cpf FROM sw_cgm_pessoa_fisica WHERE numcgm = responsavel_legal.numcgm)";
    $stSql .= "\n                     END as cpf";
    $stSql .= "\n                   , dependente.numcgm as numcgm_dependente";
    $stSql .= "\n                   , responsavel_legal.numcgm as numcgm_responsavel_legal";
    $stSql .= "\n                   , dependentes_calculados.cod_dependente";
    $stSql .= "\n                   , contrato_servidor.registro";
    $stSql .= "\n                   , agencia.num_agencia";
    $stSql .= "\n                   , agencia.cod_agencia";
    $stSql .= "\n                   , banco.num_banco";
    $stSql .= "\n                   , banco.cod_banco";
    $stSql .= "\n                   , pensao_banco.conta_corrente as nr_conta";
    $stSql .= "\n                   , dependentes_calculados.valor as proventos";
    $stSql .= "\n                   , 0 as descontos";

    if ( $this->getDado('nuPercentualPagar') != "") {
        $stSql .= "\n      , ((( dependentes_calculados.valor ) * ".$this->getDado('nuPercentualPagar').") / 100) as liquido";
    } else {
        $stSql .= "\n      , dependentes_calculados.valor as liquido ";
    }

    $stSql .= "\n                   , contrato_servidor.cod_orgao";
    $stSql .= "\n                   , contrato_servidor.cod_local";
    $stSql .= "\n                FROM ( SELECT registro";
    $stSql .= "\n                            , cod_contrato";
    $stSql .= "\n                            , cod_orgao";
    $stSql .= "\n                            , cod_local";
    $stSql .= "\n                            , cod_servidor";
    $stSql .= "\n                         FROM recuperarContratoServidor('l,o', '".Sessao::getEntidade()."', ".$this->getDado('inCodPeriodoMovimentacao').", 'geral', '".Sessao::getEntidade()."', '".Sessao::getExercicio()."')";
    $stSql .= "\n                     ) as contrato_servidor";
    $stSql .= "\n          INNER JOIN pessoal.servidor_dependente";
    $stSql .= "\n                  ON servidor_dependente.cod_servidor = contrato_servidor.cod_servidor";
    $stSql .= "\n          INNER JOIN (";
    $stSql .= "\n                       SELECT *";
    $stSql .= "\n                         FROM recuperarEventosCalculadosDependentes(".$this->getDado('inCodConfiguracao').",".$this->getDado('inCodPeriodoMovimentacao').",0,0,".$this->getDado('inCodComplementar').",'".Sessao::getEntidade()."','')";
    $stSql .= "\n                        $stSqlDesdobramento";
    $stSql .= "\n                     ) as dependentes_calculados";
    $stSql .= "\n                  ON contrato_servidor.cod_contrato = dependentes_calculados.cod_contrato";
    $stSql .= "\n             AND servidor_dependente.cod_dependente = dependentes_calculados.cod_dependente";
    $stSql .= "\n          INNER JOIN pessoal.dependente";
    $stSql .= "\n                  ON dependente.cod_dependente = servidor_dependente.cod_dependente";
    $stSql .= "\n          INNER JOIN ( SELECT pensao.*";
    $stSql .= "\n                         FROM pessoal.pensao";
    $stSql .= "\n                       , ( SELECT cod_pensao";
    $stSql .= "\n                                , MAX(timestamp) AS timestamp";
    $stSql .= "\n                                FROM pessoal.pensao";
    $stSql .= "\n                                WHERE pensao.timestamp <= ultimoTimestampPeriodoMovimentacao(".($this->getDado('inCodPeriodoMovimentacao')?$this->getDado('inCodPeriodoMovimentacao'):0).",'".Sessao::getEntidade()."')::timestamp";
    $stSql .= "\n                                GROUP BY cod_pensao) AS max_pensao";
    $stSql .= "\n                       WHERE pensao.cod_pensao = max_pensao.cod_pensao";
    $stSql .= "\n                         AND pensao.timestamp = max_pensao.timestamp";
    $stSql .= "\n                         AND NOT EXISTS (SELECT 1";
    $stSql .= "\n                                           FROM pessoal.pensao_excluida";
    $stSql .= "\n                                          WHERE pensao_excluida.cod_pensao = max_pensao.cod_pensao";
    $stSql .= "\n                                            AND max_pensao.timestamp <= pensao_excluida.timestamp";
    $stSql .= "\n                                        )";
    $stSql .= "\n                       ) as pensao";
    $stSql .= "\n          ON dependente.cod_dependente = pensao.cod_dependente";
    $stSql .= "\n          INNER JOIN pessoal.pensao_banco";
    $stSql .= "\n                  ON pensao_banco.cod_pensao = pensao.cod_pensao";
    $stSql .= "\n                 AND pensao_banco.timestamp = pensao.timestamp";
    $stSql .= "\n          INNER JOIN monetario.banco";
    $stSql .= "\n                  ON pensao_banco.cod_banco = banco.cod_banco";
    $stSql .= "\n          INNER JOIN monetario.agencia";
    $stSql .= "\n                  ON pensao_banco.cod_agencia = agencia.cod_agencia";
    $stSql .= "\n                 AND pensao_banco.cod_banco = agencia.cod_banco";
    $stSql .= "\n           LEFT JOIN pessoal.responsavel_legal";
    $stSql .= "\n                  ON responsavel_legal.cod_pensao = pensao.cod_pensao";
    $stSql .= "\n                    AND responsavel_legal.timestamp = pensao.timestamp";
    $stSql .= "\n                ) as remessa ";

    $stSqlFiltro = "";

    if ($this->getDado('inCodBanco') != "") {
        //Se for passado apenas um ID executa id, senão executa else onde os id's são inseridos dentro do IN
        if (is_numeric($this->getDado('inCodBanco'))) {
            $stSqlFiltro .= " AND remessa.cod_banco = ".$this->getDado('inCodBanco');
        } else {
            $stSqlFiltro .= " AND remessa.cod_banco IN (".$this->getDado('inCodBanco').")";
        }
    }

    if ($this->getDado('nuLiquidoMinimo') != "" && $this->getDado('nuLiquidoMaximo') != "") {
        $stSqlFiltro .= " AND (remessa.proventos - remessa.descontos) BETWEEN ".$this->getDado('nuLiquidoMinimo')." AND ".$this->getDado('nuLiquidoMaximo');
    }

    $stSqlFiltro .= " AND remessa.liquido > 0 ";

    $stSql .= " WHERE ".substr($stSqlFiltro, 4);

    return $stSql;
}

}

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
    * Classe de mapeamento da tabela FOLHAPAGAMENTO.CONFIGURACAO_EVENTO_CASO
    * Data de Criação: 26/08/2005

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Eduardo Antunez

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-11-20 15:48:41 -0200 (Ter, 20 Nov 2007) $

    * Casos de uso: uc-04.05.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  FOLHAPAGAMENTO.CONFIGURACAO_EVENTO_CASO
  * Data de Criação: 26/08/2005

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Eduardo Antunez

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoConfiguracaoEventoCaso extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoConfiguracaoEventoCaso()
{
    parent::Persistente();
    $this->setTabela('folhapagamento.configuracao_evento_caso');

    $this->setCampoCod('cod_caso');
    $this->setComplementoChave('cod_evento,cod_configuracao,timestamp');

    $this->AddCampo('cod_caso','integer',true,'',true,false);
    $this->AddCampo('cod_evento','integer',true,'',true,true);
    $this->AddCampo('timestamp','timestamp',true,'',true,true);
    $this->AddCampo('cod_configuracao','integer',true,'',true,true);
    $this->AddCampo('cod_funcao','integer',true,'',false,true);
    $this->AddCampo('cod_modulo','integer','true','',false,true);
    $this->AddCampo('cod_biblioteca','integer','true','',false,true);
    $this->AddCampo('descricao','char',true,'80',false,false);
    $this->AddCampo('proporcao_adiantamento','boolean',true,'',false,false);
    $this->AddCampo('proporcao_abono','boolean',true,'',false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT configuracao_evento_caso.* \n";
    $stSql .= "  FROM folhapagamento.configuracao_evento_caso \n";
    $stSql .= "     , (SELECT cod_evento\n";
    $stSql .= "             , max(timestamp) as timestamp\n";
    $stSql .= "          FROM folhapagamento.configuracao_evento_caso\n";
    $stSql .= "        GROUP BY cod_evento) as max_configuracao_evento_caso \n";
    $stSql .= " WHERE configuracao_evento_caso.cod_evento = max_configuracao_evento_caso.cod_evento \n";
    $stSql .= "   AND configuracao_evento_caso.timestamp  = max_configuracao_evento_caso.timestamp \n";

    return $stSql;
}

/**
    * Recupera a consulta para relacionar os eventos já cadastrados ao novo cargo criado
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  Integer $inCodCargo Parâmetro Codigo do cargo
    * @param  string $stFiltro o critério que delimita a busca
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function recuperaRelacionamentoCargoEventos(&$rsRecordSet, $inCodCargo="", $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRelacionamentoCargoEventos($inCodCargo).$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Monta consulta para relacionar os eventos já cadastrados ao novo cargo criado
    * @access Public
    * @param  Integer $inCodCargo Parâmetro Codigo do cargo
    * @return String stSql
*/
function montaRelacionamentoCargoEventos($inCodCargo)
{
    $stSql = "   SELECT cod_caso                                                    \n";
    $stSql .= "    , cod_evento                                                     \n";
    $stSql .= "    , timestamp                                                      \n";
    $stSql .= "    , cod_configuracao                                               \n";
    $stSql .= "    , ".$inCodCargo." as cod_cargo                                   \n";
    $stSql .= " from folhapagamento.configuracao_evento_caso t1                     \n";
    $stSql .= "where timestamp = (select max(timestamp)                             \n";
    $stSql .= "                     from folhapagamento.configuracao_evento_caso t2 \n";
    $stSql .= "                    where t1.cod_evento = t2.cod_evento              \n";
    //$stSql .= "                      and t1.cod_caso = t2.cod_caso                  \n";
    $stSql .= "                      and t1.cod_configuracao = t2.cod_configuracao) \n";

    return $stSql;
}

}

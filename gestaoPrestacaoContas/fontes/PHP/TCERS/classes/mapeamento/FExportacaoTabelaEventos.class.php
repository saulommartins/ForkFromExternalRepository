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
    * Classe de mapeamento da tabela folha_pagamento.eventos
    * Data de Criação: 05/03/2009

    * @author Analista: Tonismar Bernardo
    * @author Desenvolvedor: André Machado

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-02.08.01                uc-02.08.07

    $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FExportacaoTabelaEventos extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FExportacaoTabelaEventos()
{
    parent::Persistente();
    $this->setTabela('folhapagamento.evento');
    $this->AddCampo('cod_entidade', 'varchar', false, '', false, false);
    $this->AddCampo('dt_inicial', 'varchar', false, '', false, false);
}

function montaRecuperaDadosExportacao()
{
    $stSql  = "SELECT                                                                                                                                            \n";
    $stSql .= "'".$this->getDado('dt_inicial')."' as dt_inicial                                                                                                 \n";
    $stSql .= ",SUBSTR(folhapagamento".$this->getDado('cod_entidade').".evento.codigo,2) as codigo                                                               \n";
    $stSql .= ",folhapagamento".$this->getDado('cod_entidade').".evento.descricao                                                                                 \n";
    $stSql .= ",'NÃO INFORMADO' as base                                                                                                                           \n";
    $stSql .= "FROM folhapagamento".$this->getDado('cod_entidade').".evento                                                                                      \n";
    $stSql .= "INNER JOIN folhapagamento".$this->getDado('cod_entidade').".evento_evento                                                                         \n";
    $stSql .= "ON folhapagamento".$this->getDado('cod_entidade').".evento.cod_evento = folhapagamento".$this->getDado('cod_entidade').".evento_evento.cod_evento \n";

    return $stSql;
}

/**
    * Executa funcao fn_exportacao_receita no banco de dados a partir do comando SQL montado no método montaRecuperaDadosLiquidacao.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDadosExportacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaDadosExportacao().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

}

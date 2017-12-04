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
    * Classe de mapeamento da tabela TCERN.RECEITA_TC
    * Data de Criação: 17/04/2008

    * @author Analista:      Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTRNReceitaTC.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-06.08.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTRNReceitaTC extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTRNReceitaTC()
{
    parent::Persistente();
    $this->setTabela('tcern.receita_tc');

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio, cod_receita');
    $this->AddCampo('exercicio'   ,'char'    ,true, '4', true,  true);
    $this->AddCampo('cod_receita' ,'ingeger' ,true, '' , true,  true);
    $this->AddCampo('cod_tc'      ,'integer' ,true, '9', false, false);
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosExportacao.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaListagemReceita(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaListagemReceita().$stCondicao.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaListagemReceita()
{
    $stSql  = "\n  SELECT conta_receita.cod_estrutural"
              ."\n      , conta_receita.cod_conta"
              ."\n      , conta_receita.descricao"
              ."\n      , receita.cod_receita"
              ."\n      , receita_tc.cod_tc"
              ."\n FROM orcamento.receita"
              ."\n LEFT JOIN tcern.receita_tc "
              ."\n        ON receita_tc.exercicio = receita.exercicio"
              ."\n       AND receita_tc.cod_receita = receita.cod_receita"
              ."\n INNER JOIN orcamento.conta_receita"
              ."\n         ON conta_receita.cod_conta = receita.cod_conta"
              ."\n        AND conta_receita.exercicio= receita.exercicio"
              ."\n WHERE receita.exercicio = '".$this->getDado('exercicio')."'"
          ."\n ORDER BY conta_receita.cod_estrutural";

    return $stSql;
}

}

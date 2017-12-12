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
    * Classe de mapeamento da tabela ORCAMENTO_POSICAO_RECEITA
    * Data de Criação: 19/07/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Marcelo B. Paulino

    $Id: TOrcamentoPosicaoReceita.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.01.01
                    uc-02.01.04
                    uc-02.01.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TOrcamentoPosicaoReceita extends Persistente
{
function TOrcamentoPosicaoReceita()
{
    parent::Persistente();
    $this->setTabela('orcamento.posicao_receita');
    $this->setCampoCod('cod_posicao');
    $this->setComplementoChave('exercicio,cod_tipo');

    $this->AddCampo('cod_posicao', 'integer', true, ''  , true ,  false );
    $this->AddCampo('exercicio'  , 'char'   , true, '4' , true ,  false );
    $this->AddCampo('cod_tipo'  , 'integer' , true, '' ,  true ,  false );
    $this->AddCampo('mascara'    , 'char'   , true, '10', false,  false );
}

function recuperaMaxPosicao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if($this->getDado('dedutora'))
         $stCondicao .= ' AND cod_tipo = 1 ';
    else $stCondicao .= ' AND cod_tipo = 0 ';

    $stSql = $this->montaRecuperaMaxPosicao().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaMaxPosicao()
{
    $stSql = " SELECT MAX(cod_posicao) as max_posicao
                 FROM orcamento.posicao_receita
                WHERE cod_posicao is not null AND ";

    if ($this->getDado('exercicio')) {
        $stSql .= ' exercicio = \''.$this->getDado('exercicio').'\'';
    }

    return $stSql;
}

// retorna a mascara de Niveis de Hierarquia
function recuperaMascara($stExercicio , $boTransacao = "")
{
    $this->setDado('exercicio' , $stExercicio );

    $obErro = $this->recuperaMaxPosicao( $rsPosicao , '' ,'', $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $inMaxPosicao = $rsPosicao->getCampo( 'max_posicao' );

        $stMascara = "";

        for ($inCountPosicao = 1; $inCountPosicao <= $inMaxPosicao; $inCountPosicao++) {
            $this->setDado( 'cod_posicao' , $inCountPosicao );
            if($this->getDado('dedutora'))
                 $this->setDado('cod_tipo', 1);
            else $this->setDado('cod_tipo','0');
            $obErro = $this->recuperaPorChave( $rsMascara , $boTransacao );
            if ( !$obErro->ocorreu() ) {
                if ( $rsMascara->getNumLinhas() != -1 ) {
                    $stMascara .= $rsMascara->getCampo('mascara').".";
                }
            }
        }
        $stMascara  = substr( $stMascara, 0, strlen($stMascara) -1 );
    }

    return $stMascara;
}

// Métodos para deletar
function recuperaDeletaMaxPosicao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if($this->getDado('dedutora'))
         $stCondicao = ' WHERE cod_tipo = 1 ';
    else $stCondicao = ' WHERE cod_tipo = 0 ';
    $stSql = $this->montaSomaMascaraReceita().$stCondicao.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaDeletaPosicao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if($this->getDado('dedutora'))
         $stCondicao = ' AND cod_tipo = 1 ';
    else $stCondicao = ' AND cod_tipo = 0 ';

    $stSql = $this->montaDeletaMascaraReceita().$stCondicao.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function deletaMascaraReceita($stExercicio, $inPosicao)
{
    $this->setDado('exercicio'   , $stExercicio );
    $this->setDado('cod_posicao' , $inPosicao );
    $stCondicao  = " WHERE exercicio = '".$this->getDado('exercicio')."'";
    $this->montaDeletaMascaraReceita();
    $inMaxPosicao = $rsPosicao->getCampo( 'max_posicao' );

    for ($inCountPosicao = 1; $inCountPosicao <= $inMaxPosicao; $inCountPosicao++) {
        $this->setDado( 'cod_posicao' , $inCountPosicao );
        $this->recuperaDeletaPosicao( $rsTeste, '', '', $boTransacao );
    }

    return $obErro;
}

function montaSomaMascaraReceita()
{
    $stSql .= "SELECT MAX(cod_posicao) as max_posicao FROM ". $this->getTabela();

    return $stSql;
}

function montaDeletaMascaraReceita()
{
    $stSql .= "DELETE FROM ".$this->getTabela()."
                WHERE exercicio = '". $this->getDado('exercicio') ."'
                  AND cod_posicao =". $this->getDado('cod_posicao');

    return $stSql;
}

}

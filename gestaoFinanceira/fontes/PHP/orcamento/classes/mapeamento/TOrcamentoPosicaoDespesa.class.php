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
    * Classe de mapeamento da tabela ORCAMENTO_POSICAO_DESPESA
    * Data de Criação: 16/07/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Marcelo B. Paulino

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: tonismar $
    $Date: 2008-03-25 15:58:27 -0300 (Ter, 25 Mar 2008) $

    * Casos de uso: uc-02.01.01
                    uc-02.01.04
*/

/*
$Log$
Revision 1.7  2006/07/05 20:42:02  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TOrcamentoPosicaoDespesa extends Persistente
{
function TOrcamentoPosicaoDespesa()
{
    parent::Persistente();
    $this->setTabela('orcamento.posicao_despesa');
    $this->setCampoCod('cod_posicao');
    $this->setComplementoChave('exercicio');

    $this->AddCampo('cod_posicao', 'integer' , true, ''  , true ,  false );
    $this->AddCampo('exercicio'  , 'char'    , true, '4' , true ,  false );
    $this->AddCampo('mascara'    , 'integer' , true, '10', false,  false );
}

function recuperaMaxPosicao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaMaxPosicao().$stCondicao.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaMaxPosicao()
{
    $stSql = "SELECT MAX(cod_posicao) as max_posicao FROM orcamento.posicao_despesa";

    return $stSql;
}

// retorna a mascara de Niveis de Hierarquia
function recuperaMascara($stExercicio , $boTransacao = "")
{
    $this->setDado('exercicio' , $stExercicio );
    $stCondicao = " WHERE exercicio = '".$this->getDado('exercicio')."'";

    $obErro = $this->recuperaMaxPosicao( $rsPosicao , $stCondicao, '', $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $inMaxPosicao = $rsPosicao->getCampo( 'max_posicao' );
        $stMascara = "";
        isset($stMascara) ? $stMascara : ($stMascara = '');

        for ($inCountPosicao = 1; $inCountPosicao <= $inMaxPosicao; $inCountPosicao++) {
            $this->setDado( 'cod_posicao' , $inCountPosicao );
            $this->recuperaPorChave( $rsMascara, $boTransacao );
            if ( $rsMascara->getNumLinhas() != -1 ) {
                $stMascara .= $rsMascara->getCampo('mascara').".";
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
    $stSql = $this->montaSomaMascaraDespesa().$stCondicao.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaDeletaPosicao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaDeletaMascaraDespesa().$stCondicao.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function deletaMascaraDespesa($stExercicio)
{
    $this->setDado('exercicio' , $stExercicio );
//    $stCondicao = " exercicio = ".$this->getDado('exercicio');

    $this->recuperaDeletaMaxPosicao( $rsPosicao , $stCondicao );
    $inMaxPosicao = $rsPosicao->getCampo( 'max_posicao' );

    for ($inCountPosicao = 1; $inCountPosicao <= $inMaxPosicao; $inCountPosicao++) {
        $this->setDado( 'cod_posicao' , $inCountPosicao );
        $this->recuperaDeletaPosicao( $rsTeste );
    }

    return $obErro;
}

function montaSomaMascaraDespesa()
{
    $stSql .= "SELECT MAX(cod_posicao) as max_posicao FROM ". $this->getTabela();

    return $stSql;
}

function montaDeletaMascaraDespesa()
{
    $stSql .= "DELETE FROM ".$this->getTabela()." WHERE exercicio = '". $this->getDado('exercicio') ."' AND cod_posicao =". $this->getDado('cod_posicao');

    return $stSql;
}

}

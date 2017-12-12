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
    * Classe de mapeamento da função EstornoRealizacaoReceitaFixa
    * Data de Criação: 20/12//2005

    * @author Analista: Lucas Leusin Oiagem
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2007-07-13 16:18:44 -0300 (Sex, 13 Jul 2007) $

    * Casos de uso: uc-02.04.08
*/

/*
$Log$
Revision 1.4  2007/07/13 19:10:48  cako
Bug#9383#, Bug#9384#

Revision 1.3  2006/07/05 20:38:37  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FTesourariaEstornoRealizacaoReceitaFixa extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FTesourariaEstornoRealizacaoReceitaFixa()
{
    parent::Persistente();
    $this->setTabela('EstornoRealizacaoReceitaFixa');

    $this->AddCampo('exercicio'              ,'varchar',false,''    ,false,false);
    $this->AddCampo('valor'                  ,'numeric',false,'14.2',false,false);
    $this->AddCampo('complemento'            ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_lote'               ,'integer',false,''    ,false,false);
    $this->AddCampo('tipo_lote'              ,'integer',false,''    ,false,false);
    $this->AddCampo('dt_lote'                ,'date'   ,false,''    ,false,false);
    $this->AddCampo('cod_entidade'           ,'integer',false,''    ,false,false);
    $this->AddCampo('valor_despesa'          ,'numeric',false,'14.2',false,false);
    $this->AddCampo('valor_disponibilidades' ,'numeric',false,'14.2',false,false);
}

function montaInsereLote()
{
    $stSql  = "SELECT                                    \n";
    $stSql .= "      contabilidade.fn_insere_lote(       \n";
    $stSql .= "  '".$this->getDado('exercicio'   )."'    \n";
    $stSql .= " , ".$this->getDado('cod_entidade')."     \n";
    $stSql .= " ,'".$this->getDado('tipo_lote'   )."'    \n";
    $stSql .= " ,'".$this->getDado('nom_lote'    )."'    \n";
    $stSql .= " ,'".$this->getDado('dt_lote'     )."'    \n";
    $stSql .= " ) AS cod_lote                            \n";

    return $stSql;
}

function montaExecutaFuncao($boTransacao = "")
{
    $stSql  = " SELECT                                                                  \n";
    $stSql .= "       EstornoRealizacaoReceitaFixa(                                     \n";
    $stSql .= "                        '".$this->getDado("exercicio")              ."', \n";
    $stSql .= "                         ".$this->getDado('valor')                  ." , \n";
    $stSql .= "                        '".$this->getDado("complemento")            ."', \n";
    $stSql .= "                         ".$this->getDado("cod_lote")               ." , \n";
    $stSql .= "                        '".$this->getDado("tipo_lote")              ."', \n";
    $stSql .= "                         ".$this->getDado("cod_entidade")           ." , \n";
    if ( !Sessao::getExercicio() > '2012' ) {
        $stSql .= "                         ".$this->getDado('valor_despesa')          ." , \n";
    }
    if ( !Sessao::getExercicio() > '2012' ) {
        $stSql .= "                         ".$this->getDado('valor_disponibilidades') ." , \n";
    }
    if($this->getDado('cod_historico'))
                            $stSql .= " ".$this->getDado('cod_historico')      ." ) \n";
    else                    $stSql .= " null ) \n";
    $stSql .= "                         as sequencia                                    \n";

    return $stSql;
}

/**
    * Executa funcao EmpenhoEmissao no banco de dados a partir do comando SQL montado no método montaExecutaFuncao.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function executaFuncao(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if ( !$this->getDado("cod_lote") ) {
        $stSql = $this->montaInsereLote();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSetLote, $stSql, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->setDado("cod_lote", $rsRecordSetLote->getCampo("cod_lote"));
        }
    }
    if ( !$obErro->ocorreu() ) {
        $stSql = $this->montaExecutaFuncao($boTransacao);
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->setDado("sequencia", $rsRecordSet->getCampo("sequencia"));
        }
    }

    return $obErro;
}

}

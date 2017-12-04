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
    * Classe de mapeamento da tabela FN_EMPENHO_EMPENHO_ESTORNO_RESTOS_A_PAGAR
    * Data de Criação: 29/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.03.03
*/

/*
$Log$
Revision 1.8  2006/07/05 20:46:56  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FEmpenhoEmpenhoEstornoRestosAPagar extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FEmpenhoEmpenhoEstornoRestosAPagar()
{
    parent::Persistente();
    $this->setTabela('EmpenhoEstornoRestosAPagar');

    $this->AddCampo('exercicio'              ,'varchar',false,''    ,false,false);
    $this->AddCampo('valor'                  ,'numeric',false,'14.2',false,false);
    $this->AddCampo('complemento'            ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_lote'               ,'integer',false,''    ,false,false);
    $this->AddCampo('tipo_lote'              ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_entidade'           ,'integer',false,''    ,false,false);
    $this->AddCampo('cod_pre_empenho'        ,'integer',false,''    ,false,false);
    $this->AddCampo('exerc_rp'               ,'varchar',false,''    ,false,false);
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

function montaExecutaFuncao()
{
    $stSql  = " SELECT  \n";
    $stSql .= " ".$this->getTabela()."('".$this->getDado("exercicio")        ."', \n";
    $stSql .= "                         ".$this->getDado("valor")            ." , \n";
    $stSql .= "                        '".$this->getDado("complemento")      ."', \n";
    $stSql .= "                         ".$this->getDado("cod_lote")         ." , \n";
    $stSql .= "                        '".$this->getDado("tipo_lote")        ."', \n";
    $stSql .= "                         ".$this->getDado("cod_entidade")     ." , \n";
    $stSql .= "                         ".$this->getDado("cod_pre_empenho")  ." , \n";
    $stSql .= "                        '".$this->getDado("exerc_rp")         ."') \n";
    $stSql .= "                         as sequencia                              \n";

    return $stSql;
}

function montaExecutaFuncaoTCEMS()
{
    $stSql  = " SELECT  \n";
    $stSql .= " empenhoestornorestosapagarTCEMS ('".$this->getDado("exercicio")        ."',  \n";
    $stSql .= "                                   ".$this->getDado("valor")            ." ,  \n";
    $stSql .= "                                  '".$this->getDado("complemento")      ."',  \n";
    $stSql .= "                                   ".$this->getDado("cod_lote")         ." ,  \n";
    $stSql .= "                                  '".$this->getDado("tipo_lote")        ."',  \n";
    $stSql .= "                                   ".$this->getDado("cod_entidade")     ." ,  \n";
    $stSql .= "                                   ".$this->getDado("cod_pre_empenho")  ." ,  \n";
    $stSql .= "                                  '".$this->getDado("exerc_rp")         ."',  \n";
    $stSql .= "                                  '".Sessao::read('inRestos')           ."')  \n";
    $stSql .= "                                   as sequencia                               \n";

    return $stSql;
}

/**
    * Executa funcao EmpenhoEmissao no banco de dados a partir do comando SQL montado no método montaExecutaFuncao.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
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

        if ((Sessao::getExercicio() > '2012') && ($this->getDado("exerc_rp") < Sessao::getExercicio())) {
            $stSql = $this->montaExecutaFuncaoTCEMS();
        } else {
            $stSql = $this->montaExecutaFuncao();
        }

        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->setDado("sequencia", $rsRecordSet->getCampo("sequencia"));
        }
    }

    return $obErro;
}

function verificaConta(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaVerificaConta();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaVerificaConta()
{
    $stSql = "SELECT plano_analitica.cod_plano 
                                 FROM contabilidade.plano_conta 
                                 JOIN contabilidade.plano_analitica
                                   ON plano_analitica.cod_conta = plano_conta.cod_conta
                                  AND plano_analitica.exercicio = plano_conta.exercicio
                                WHERE plano_conta.exercicio = '".$this->getDado('exercicio')."'
                                  AND plano_conta.cod_estrutural LIKE '".$this->getDado('cod_estrutural')."%'";
    
    return $stSql;
}

function buscaContaComMascara(&$rsRecordSet,$boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaBuscaContaComMascara();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaBuscaContaComMascara (){
    
  $stSql = " SELECT
                   publico.fn_mascara_completa((select valor from administracao.configuracao where cod_modulo = 9 and
                    exercicio = '".$this->getDado('exercicio')."' and parametro = 'masc_plano_contas'), '".$this->getDado('cod_estrutural')."') ";
                    
 return $stSql;
            
}
}

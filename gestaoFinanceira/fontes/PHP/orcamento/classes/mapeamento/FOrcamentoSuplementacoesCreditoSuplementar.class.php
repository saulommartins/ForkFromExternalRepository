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
    * Classe de mapeamento da tabela FN_ORCAMENTO_SUPLEMENTACOES_CREDITO_SUPLEMENTAR
    * Data de Criação: 23/02/2005

    * @author Analista: Jorge Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.24
                    uc-02.01.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FOrcamentoSuplementacoesCreditoSuplementar extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
public function __construct()
{
    parent::Persistente();
    $this->setTabela('OrcamentoSuplementacoesCreditoSuplementar');

    $this->AddCampo('exercicio'              ,'varchar',false,''    ,false,false);
    $this->AddCampo('valor'                  ,'numeric',false,'14.2',false,false);
    $this->AddCampo('complemento'            ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_lote'               ,'integer',false,''    ,false,false);
    $this->AddCampo('tipo_lote'              ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_entidade'           ,'integer',false,''    ,false,false);
    $this->AddCampo('cred_suplementar'       ,'varchar',false,''    ,false,false);
}

public function montaExecutaFuncao()
{
    $stSql  = " SELECT  \n";
    $stSql .= " ".$this->getTabela()."('".$this->getDado("exercicio")        ."', \n";
    $stSql .= "                         ".$this->getDado("valor")            ." , \n";
    $stSql .= "                        '".$this->getDado("complemento")      ."', \n";
    $stSql .= "                         ".$this->getDado("cod_lote")         ." , \n";
    $stSql .= "                        '".$this->getDado("tipo_lote")        ."', \n";
    $stSql .= "                         ".$this->getDado("cod_entidade")     ." , \n";
    $stSql .= "                        '".$this->getDado("cred_suplementar") ."') \n";
    $stSql .= "                         as sequencia                              \n";

    return $stSql;
}

public function montaInsereLote()
{
    $stSql  = "SELECT                                                             \n";
    $stSql .= " contabilidade.fn_insere_lote(                                     \n";
    $stSql .= "                               '".$this->getDado('exercicio')  ."' \n";
    $stSql .= "                              ,".$this->getDado('cod_entidade')."  \n";
    $stSql .= "                              ,'".$this->getDado('tipo_lote')  ."' \n";
    $stSql .= "                              ,'".$this->getDado('complemento')."' \n";
    $stSql .= "                              ,'".$this->getDado('dt_lote')    ."' \n";
    $stSql .= " ) as cod_lote                                                     \n";

    return $stSql;
}

/**
    * Executa funcao EmpenhoEmissao no banco de dados a partir do comando SQL montado no método montaExecutaFuncao.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
public function executaFuncao(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if ( !$this->getDado('cod_lote') ) {
        $stSql = $this->montaInsereLote();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->setDado('cod_lote', $rsRecordSet->getCampo('cod_lote') );
        }
    }

    if ( !$obErro->ocorreu() ) {
        $stSql = $this->montaExecutaFuncao();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    }

    return $obErro;
}

}

?>
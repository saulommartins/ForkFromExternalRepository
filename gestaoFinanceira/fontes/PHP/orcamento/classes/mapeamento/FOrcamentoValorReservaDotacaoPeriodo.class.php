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
    * Classe de mapeamento da tabela FN_ORCAMENTO_VALOR_RESERVA_DOTACAO
    * Data de Criação: 24/01/2005

    * @author Analista: Jorge Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Id: FOrcamentoValorReservaDotacaoPeriodo.class.php 65434 2016-05-20 18:32:34Z michel $

    * Casos de uso: uc-02.01.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class FOrcamentoValorReservaDotacaoPeriodo extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FOrcamentoValorReservaDotacaoPeriodo()
{
    parent::Persistente();
    $this->setTabela('orcamento.fn_consultar_valor_reserva_dotacao_periodo');

    $this->AddCampo('exercicio'   ,'varchar',false,'' ,false,false);
    $this->AddCampo('cod_despesa' ,'integer',false,'' ,false,false);
    $this->AddCampo('dt_final'    ,'varchar',false,'' ,false,false);
}

function montaExecutaFuncao()
{
    $stSql  = " SELECT                                                        \n";
    $stSql .= " ".$this->getTabela()."('".$this->getDado("exercicio")   ."',  \n";
    $stSql .= "                         ".$this->getDado("cod_despesa") ." ,  \n";
    $stSql .= "                        '".$this->getDado("dt_final")    ."' ) \n";
    $stSql .= " AS valor_reserva_dotacao                                      \n";

    return $stSql;
}

/**
    * Executa funcao executaFuncao no banco de dados a partir do comando SQL montado no método montaExecutaFuncao.
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

    $stSql = $this->montaExecutaFuncao();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

}

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
/*
 * Arquivo de mapeamento da tabela tcepb.relacao_conta_corrente_fonte_pagadora
 * Data de Criação   : 18/02/2009

 * @author Analista      Tonismar Regis Bernardo
 * @author Desenvolvedor Eduardo Paculski Schitz

 * @package URBEM
 * @subpackage

 * @ignore

 $Id:$
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTPBRelacaoContaBancariaFontePagadora extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTPBRelacaoContaBancariaFontePagadora()
{
    parent::Persistente();
    $this->setTabela("tcepb.relacao_conta_corrente_fonte_pagadora");

    $this->setCampoCod('cod_tipo');
    $this->setComplementoChave('exercicio,cod_conta_corrente');

    $this->AddCampo('cod_banco'         , 'integer', true, ''  ,true ,true);
    $this->AddCampo('cod_agencia'       , 'integer', true, ''  ,true ,true);
    $this->AddCampo('cod_conta_corrente', 'integer', true, ''  ,true ,true);
    $this->AddCampo('cod_tipo'          , 'integer', true, ''  ,true ,true);
    $this->AddCampo('exercicio'         , 'varchar', true, '4' ,true ,true);
}

function montaRecuperaTodos()
{
    $stSql  = " SELECT *                                                \n";
    $stSql .= "   FROM tcepb.relacao_conta_corrente_fonte_pagadora      \n";
    $stSql .= "  WHERE exercicio = ".$this->getDado('exercicio')."      \n";

    return $stSql;
}

function recuperaTodosFiltrado(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaTodosFiltrado().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaTodosFiltrado()
{
    $stSql  = " SELECT *                                                
                  FROM tcepb.relacao_conta_corrente_fonte_pagadora      
                 WHERE exercicio = '".$this->getDado('exercicio')."'      
                   AND cod_tipo  = ".$this->getDado('cod_tipo');

    return $stSql;
}

function recuperaRelacionamentoCCorrenteFontePagadora(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaRelacionamentoCCorrenteFontePagadora().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoCCorrenteFontePagadora()
{
    $stSql  = " SELECT LPAD(REPLACE(conta_corrente.num_conta_corrente, '-', ''),12,'0') AS num_conta_corrente
                     , relacao_conta_corrente_fonte_pagadora.cod_tipo           
                  FROM tcepb.relacao_conta_corrente_fonte_pagadora               
        
            INNER JOIN monetario.conta_corrente                                  
                    ON conta_corrente.cod_conta_corrente = relacao_conta_corrente_fonte_pagadora.cod_conta_corrente 
                   AND conta_corrente.cod_agencia        = relacao_conta_corrente_fonte_pagadora.cod_agencia 
                   AND conta_corrente.cod_banco          = relacao_conta_corrente_fonte_pagadora.cod_banco 
        
                 WHERE relacao_conta_corrente_fonte_pagadora.exercicio = '".$this->getDado('exercicio')."' ";

    return $stSql;
}

}

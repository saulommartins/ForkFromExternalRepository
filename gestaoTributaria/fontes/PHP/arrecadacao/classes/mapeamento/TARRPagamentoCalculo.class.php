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
    * Classe de mapeamento da tabela ARRECADACAO.PAGAMENTO_CALCULO
    * Data de Criação: 12/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRPagamentoCalculo.class.php 64341 2016-01-15 20:11:16Z evandro $

* Casos de uso: uc-05.03.10
*/

/*
$Log$
Revision 1.8  2006/09/15 11:50:01  fabio
corrigidas tags de caso de uso

Revision 1.7  2006/09/15 10:41:36  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ARRECADACAO.PAGAMENTO_CALCULO
  * Data de Criação: 18/05/2005

  * @author Analista: Fabio Bertoldi
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TARRPagamentoCalculo extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TARRPagamentoCalculo()
{
    parent::Persistente();
    $this->setTabela('arrecadacao.pagamento_calculo');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_calculo,numeracao,ocorrencia_pagamento,exercicio');

    $this->AddCampo('cod_calculo','integer',true,'',true,true);
    $this->AddCampo('numeracao','varchar',true,'17',true,true);
    $this->AddCampo('ocorrencia_pagamento','integer',true,'',true,true);
    $this->AddCampo('cod_convenio','integer',true,'',true,true);
    $this->AddCampo('valor','numeric',true,'14,2',false,false);
}

function recuperaTotalPagamentoCalculo(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaTotalPagamentoCalculo($stFiltro,$stOrdem);
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaTotalPagamentoCalculo($stFiltro,$stOrdem)
{
    $stSql = "SELECT MAX(cod_calculo) as cod_calculo
                    ,pagamento_calculo.numeracao
                    ,pagamento_calculo.ocorrencia_pagamento
                    ,pagamento_calculo.valor
                    ,pagamento_calculo.cod_convenio
                FROM arrecadacao.pagamento_calculo 
                ".$stFiltro."
                GROUP BY numeracao
                        ,ocorrencia_pagamento
                        ,valor
                        ,cod_convenio
                ".$stOrdem." 
                ";
    return $stSql;
}

}//END OF CLASS
?>

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
  * Classe de mapeamento da tabela ARRECADACAO.IMOVEL_CALCULO
    * Data de Criação: 12/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRImovelCalculo.class.php 65602 2016-06-01 17:44:39Z evandro $

* Casos de uso: uc-05.03.05
*/

/*
$Log$
Revision 1.10  2007/03/21 21:12:19  dibueno
Bug #8845#

Revision 1.9  2006/09/15 11:50:01  fabio
corrigidas tags de caso de uso

Revision 1.8  2006/09/15 10:41:36  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ARRECADACAO.IMOVEL_CALCULO
  * Data de Criação: 18/05/2005

  * @author Analista: Fabio Bertoldi
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TARRImovelCalculo extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TARRImovelCalculo()
{
    parent::Persistente();
    $this->setTabela('arrecadacao.imovel_calculo');

    $this->setCampoCod('cod_calculo');
    $this->setComplementoChave('cod_calculo,inscricao_municipal');
    
    $this->AddCampo('cod_calculo','integer',true,'',true,true);
    $this->AddCampo('inscricao_municipal','integer',true,'',true,true);
    $this->AddCampo('timestamp','timestamp',false,'',false,true);

}

function recuperaTimestampImovelCalculo(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaTimestampImovelCalculo().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaTimestampImovelCalculo()
{
    $stSql .= " SELECT                                      \n";
    $stSql .= "     max(IC.timestamp) as timestamp          \n";
    $stSql .= " FROM                                        \n";
    $stSql .= "     arrecadacao.imovel_calculo AS IC        \n";

    return $stSql;
}

function recuperaTimestampImovelVenal(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaTimestampImovelVenal().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaTimestampImovelVenal()
{
    $stSql .= " SELECT                                      \n";
    $stSql .= "     max(IC.timestamp) as timestamp          \n";
    $stSql .= " FROM                                        \n";
    $stSql .= "     arrecadacao.imovel_v_venal AS IC        \n";

    return $stSql;
}

function recuperaCalculosAbertoImovel(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaCalculosAbertoImovel().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    #$this->debug();
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaCalculosAbertoImovel()
{
    $stSql .= " SELECT                                                                          \n";
    $stSql .= "     calc.cod_calculo                                                            \n";
    $stSql .= "     , aic.inscricao_municipal                                                   \n";
    $stSql .= "     , accgm.numcgm                                                              \n";
    $stSql .= " FROM                                                                            \n";
    $stSql .= "     arrecadacao.imovel_calculo as aic                                           \n";
    $stSql .= "     INNER JOIN arrecadacao.calculo as calc                                      \n";
    $stSql .= "     ON calc.cod_calculo = aic.cod_calculo                                       \n";
    $stSql .= "     INNER JOIN arrecadacao.calculo_cgm as accgm                                 \n";
    $stSql .= "     ON accgm.cod_calculo = aic.cod_calculo                                      \n";
    $stSql .= "     INNER JOIN arrecadacao.lancamento_calculo as alc                            \n";
    $stSql .= "     ON alc.cod_calculo = aic.cod_calculo                                        \n";
    $stSql .= "     INNER JOIN arrecadacao.parcela as ap                                        \n";
    $stSql .= "     ON ap.cod_lancamento = alc.cod_lancamento                                   \n";
    $stSql .= "     INNER JOIN arrecadacao.carne as ac                                          \n";
    $stSql .= "     ON ac.cod_parcela = ap.cod_parcela                                          \n";
    $stSql .= "     LEFT JOIN arrecadacao.pagamento as apag                                     \n";
    $stSql .= "     ON apag.numeracao = ac.numeracao                                            \n";
    $stSql .= "     AND apag.cod_convenio = ac.cod_convenio                                     \n";

    return $stSql;

}

}
?>

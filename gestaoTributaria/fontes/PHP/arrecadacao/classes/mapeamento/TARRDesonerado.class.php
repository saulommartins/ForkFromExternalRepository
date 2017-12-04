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
    * Classe de mapeamento da tabela ARRECADACAO.DESONERADO
    * Data de Criação: 12/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRDesonerado.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.04
*/

/*
$Log$
Revision 1.12  2007/05/31 14:43:17  cercato
Bug #9340#

Revision 1.11  2006/11/21 15:49:13  cercato
bug #6853#

Revision 1.10  2006/11/16 16:46:17  cercato
correcao para calculo/lancamento individual cadastro economico.

Revision 1.9  2006/09/15 11:50:01  fabio
corrigidas tags de caso de uso

Revision 1.8  2006/09/15 10:40:57  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ARRECADACAO.DESONERADO
  * Data de Criação: 18/05/2005

  * @author Analista: Fabio Bertoldi
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TARRDesonerado extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TARRDesonerado()
{
    parent::Persistente();
    $this->setTabela('arrecadacao.desonerado');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_desoneracao,numcgm,ocorrencia');

    $this->AddCampo('cod_desoneracao','integer',true,'',true,true);
    $this->AddCampo('numcgm','integer',true,'',true,true);
    $this->AddCampo('data_concessao','date',true,'',false,false);
    $this->AddCampo('data_prorrogacao','date',true,'',false,false);
    $this->AddCampo('data_revogacao','date',true,'',false,false);
    $this->AddCampo('ocorrencia','integer',true,'',true,false);
}

function recuperaLancamentoDesonerado(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "" , $nuValor=0)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    //$stOrdem = $stOrdem ? $stOrdem : " ORDER BY cod_lancamento ";
    $stSql  = $this->montaRecuperaLancamentoDesonerado($nuValor).$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    //$this->debug();exit;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

    public function montaRecuperaLancamentoDesonerado($nuValor)
    {
        $stSql  = "    select lancamento_concede_desoneracao.*                                                             \n";
        $stSql .= "         , funcaoAbstrataDesoneracao ( lancamento_concede_desoneracao.cod_desoneracao, lancamento_concede_desoneracao.numcgm ,".$nuValor.") as valor \n";
        $stSql .= "      from arrecadacao.lancamento_concede_desoneracao                                                   \n";
        $stSql .= "inner join arrecadacao.lancamento_calculo                                                               \n";
        $stSql .= "        on lancamento_calculo.cod_lancamento = lancamento_concede_desoneracao.cod_lancamento            \n";
        $stSql .= "       and lancamento_calculo.cod_calculo = lancamento_concede_desoneracao.cod_calculo                  \n";
        $stSql .= "inner join arrecadacao.calculo                                                                          \n";
        $stSql .= "        on calculo.cod_calculo = lancamento_calculo.cod_calculo                                         \n";
        $stSql .= "inner join arrecadacao.desonerado                                                                       \n";
        $stSql .= "        on desonerado.cod_desoneracao = lancamento_concede_desoneracao.cod_desoneracao                  \n";
        $stSql .= "       and desonerado.numcgm = lancamento_concede_desoneracao.numcgm                                    \n";
        $stSql .= "       and desonerado.ocorrencia = lancamento_concede_desoneracao.ocorrencia                            \n";
        $stSql .= "inner join arrecadacao.desonerado_imovel                                                                \n";
        $stSql .= "        on desonerado_imovel.cod_desoneracao = desonerado.cod_desoneracao                               \n";
        $stSql .= "       and desonerado_imovel.numcgm = desonerado.numcgm                                                 \n";
        $stSql .= "       and desonerado_imovel.ocorrencia = desonerado.ocorrencia                                         \n";
        $stSql .= " left join arrecadacao.lancamento_usa_desoneracao                                                       \n";
        $stSql .= "        on lancamento_usa_desoneracao.cod_desoneracao = desonerado.cod_desoneracao                      \n";
        $stSql .= "       and lancamento_usa_desoneracao.numcgm = desonerado.numcgm                                        \n";
        $stSql .= "       and lancamento_usa_desoneracao.ocorrencia = desonerado.ocorrencia                                \n";
        $stSql .= "   where (cast(substr(cast(now() as date)::varchar,1,4) AS integer)-  cast(substr(desonerado.data_concessao::varchar, 1, 4) AS integer))  > 0  \n";
        $stSql .= "       and lancamento_usa_desoneracao.cod_desoneracao is null                                           \n";

        return $stSql;
    }

//recupera lancamento desonerado por inscricao economica
function recuperaLancamentoDesoneradoCadEco(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "" , $nuValor=0)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    //$stOrdem = $stOrdem ? $stOrdem : " ORDER BY cod_lancamento ";
    $stSql  = $this->montaRecuperaLancamentoDesoneradoCadEco($nuValor).$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    //$this->debug();exit;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaLancamentoDesoneradoCadEco($nuValor)
{
    $stSql   = " select \n";
    $stSql  .= "     lancamento_concede_desoneracao.* \n";
    $stSql  .= "     , funcaoAbstrataDesoneracao ( lancamento_concede_desoneracao.cod_desoneracao, lancamento_concede_desoneracao.numcgm ,".$nuValor." ) as valor  \n";
    $stSql  .= " from  \n";
    $stSql  .= "     arrecadacao.lancamento_concede_desoneracao \n";

    $stSql  .= " inner join  \n";
    $stSql  .= "     arrecadacao.lancamento_calculo \n";
    $stSql  .= " on  \n";
    $stSql  .= "     lancamento_calculo.cod_lancamento = lancamento_concede_desoneracao.cod_lancamento \n";
    $stSql  .= "     and lancamento_calculo.cod_calculo = lancamento_concede_desoneracao.cod_calculo \n";

    $stSql  .= " inner join  \n";
    $stSql  .= "     arrecadacao.calculo \n";
    $stSql  .= " on  \n";
    $stSql  .= "     calculo.cod_calculo = lancamento_calculo.cod_calculo \n";

    $stSql  .= " inner join  \n";
    $stSql  .= "     arrecadacao.desonerado \n";
    $stSql  .= " on  \n";
    $stSql  .= "     desonerado.cod_desoneracao = lancamento_concede_desoneracao.cod_desoneracao \n";
    $stSql  .= "     and desonerado.numcgm = lancamento_concede_desoneracao.numcgm \n";
    $stSql  .= "     and desonerado.ocorrencia = lancamento_concede_desoneracao.ocorrencia \n";

    $stSql  .= " inner join  \n";
    $stSql  .= "     arrecadacao.desonerado_cad_economico \n";
    $stSql  .= " on  \n";
    $stSql  .= "     desonerado_cad_economico.cod_desoneracao = desonerado.cod_desoneracao \n";
    $stSql  .= "     and desonerado_cad_economico.numcgm = desonerado.numcgm  \n";
    $stSql  .= "     and desonerado_cad_economico.ocorrencia = desonerado.ocorrencia \n";

    $stSql  .= " left join  \n";
    $stSql  .= "     arrecadacao.lancamento_usa_desoneracao \n";
    $stSql  .= " on  \n";
    $stSql  .= "     lancamento_usa_desoneracao.cod_desoneracao = desonerado.cod_desoneracao \n";
    $stSql  .= "     and lancamento_usa_desoneracao.numcgm = desonerado.numcgm  \n";
    $stSql  .= "     and lancamento_usa_desoneracao.ocorrencia = desonerado.ocorrencia \n";

    $stSql  .= " where  \n";
    $stSql  .= "     ( ( substr ( now()::date , 1 , 4 ) )::integer -  ( substr ( desonerado.data_concessao , 1 , 4 ) )::integer  ) > 0  \n";
    $stSql  .= "     and lancamento_usa_desoneracao.cod_desoneracao is null \n";

    return $stSql;
}

function aplicaDesoneracao(&$rsRecordSet, $inCodCredito, $inCodEspecie, $inCodGenero, $inCodNatureza, $flValor, $dtDataAtual, $inInsc, $inNumCGM, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaAplicaDesoneracao( $inCodCredito, $inCodEspecie, $inCodGenero, $inCodNatureza, $flValor, $dtDataAtual, $inInsc, $inNumCGM );
    $this->setDebug( $stSql );
    //$this->debug();exit;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaAplicaDesoneracao($inCodCredito, $inCodEspecie, $inCodGenero, $inCodNatureza, $flValor, $dtDataAtual, $inInsc, $inNumCGM)
{
    $stSql   = " SELECT \r\n"; //falta fazer a pl para aplicar desoneracao
    $stSql  .= "     aplica_desoneracao_lancamento(".$inCodCredito.", ".$inCodEspecie.", ".$inCodGenero." ,".$inCodNatureza.", ".$flValor.", '".$dtDataAtual."', ".$inInsc.", ".$inNumCGM." ) AS valor \r\n";

    return $stSql;
}

}
?>

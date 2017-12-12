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
    * Classe de mapeamento
    * Data de Criação: 26/12/2006

    * @author Analista: Muriel Preuss
    * @author Desenvolvedor: Cleisson Barboza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: tonismar $
    $Date: 2008-01-28 22:18:54 -0200 (Seg, 28 Jan 2008) $

    * Casos de uso: uc-02.02.31
*/

/*
$Log$
Revision 1.1  2007/01/03 21:58:36  cleisson
UC 02.02.31

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FContabilidadeEncerramento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FContabilidadeEncerramento()
{
    parent::Persistente();
    $this->setTabela('contabilidade.fn_gerar_restos_pagar');

}

function gerarRestosEncerramento(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaGerarRestosEncerramento();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaGerarRestosEncerramento()
{
    $stSql  = " SELECT                                                          \n";
    $stSql .= "     *                                                           \n";
    $stSql .= " FROM                                                            \n";
    if ($this->getDado("stExercicio") >= '2013') {
        $stSql .= "   ".$this->getTabela()."_2013('".$this->getDado("stExercicio")."',".$this->getDado("inCodEntidade").")   \n";
    } else {
        $stSql .= "   ".$this->getTabela()."('".$this->getDado("stExercicio")."')   \n";
    }

    return $stSql;
}

function inscreveRestosPagar($boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaInscreveRestosPagar();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaInscreveRestosPagar()
{
    $stSql = " SELECT contabilidade.fn_insere_rps('".$this->getDado("stExercicio")."', '".$this->getDado("inCodEntidade")."', '31/12/".$this->getDado("stExercicio")."')";

    return $stSql;
}

function anularRestosEncerramento(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaAnularRestosEncerramento();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaAnularRestosEncerramento()
{
    $stSql  = " SELECT                                                          \n";
    $stSql .= "     *                                                           \n";
    $stSql .= " FROM                                                            \n";
    $stSql .= "   contabilidade.fn_anular_restos_encerramento('".$this->getDado("stExercicio")."'";

    if (Sessao::getExercicio() >= '2013') {
        $stSql .= ", ".$this->getDado('inCodEntidade')."";
    }

    $stSql .=")   \n";

    return $stSql;
}

function gerarEncerramentoReceita(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaGerarEncerramentoReceita();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function gerarRestosIndividual(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaGerarRestosIndividual();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaGerarRestosIndividual()
{
    $stSql  = " SELECT      \n";
    $stSql .= "     *       \n";
    $stSql .= "   FROM      \n";
    $stSql .= "  contabilidade.fn_gerar_restos_pagar_individual( \n";
    $stSql .= "         '".$this->getDado("stExercicio")    ."', \n";
    $stSql .= "          ".$this->getDado("inEntidade")     ." , \n";
    $stSql .= "         '".$this->getDado("stCodEstDeb")    ."', \n";
    $stSql .= "         '".$this->getDado("stCodEstCred")   ."', \n";
    $stSql .= "          ".$this->getDado("nuValor_rp")     ." , \n";
    $stSql .= "          ".$this->getDado("stComplemento")  ." ) \n";

    return $stSql;
}

function montaGerarEncerramentoReceita()
{
    $stSql  = " SELECT                                                          \n";
    $stSql .= "     *                                                           \n";
    $stSql .= " FROM                                                            \n";
    $stSql .= "     contabilidade.encerramentoAnualLancamentosReceita(      \n";
    $stSql .= "   '".$this->getDado("stExercicio")."','".$this->getDado("inCodEntidade")."')\n";

    return $stSql;
}

function gerarEncerramentoDespesa(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaGerarEncerramentoDespesa();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaGerarEncerramentoDespesa()
{
    $stSql  = " SELECT                                                          \n";
    $stSql .= "     *                                                           \n";
    $stSql .= " FROM                                                            \n";
    $stSql .= "     contabilidade.encerramentoAnualLancamentosDespesa(      \n";
    $stSql .= "   '".$this->getDado("stExercicio")."','".$this->getDado("inCodEntidade")."')\n";

    return $stSql;
}

function gerarEncerramentoVariacoes(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaGerarEncerramentoVariacoes();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaGerarEncerramentoVariacoes()
{
    $stSql  = " SELECT                                                          \n";
    $stSql .= "     *                                                           \n";
    $stSql .= " FROM                                                            \n";
    $stSql .= "     contabilidade.encerramentoAnualLancamentosVariacoesPatri(      \n";
    $stSql .= "   '".$this->getDado("stExercicio")."','".$this->getDado("inCodEntidade")."')\n";

    return $stSql;
}

function gerarEncerramentoOrcamentario(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaGerarEncerramentoOrcamentario();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaGerarEncerramentoOrcamentario()
{
    $stSql  = " SELECT                                                          \n";
    $stSql .= "     *                                                           \n";
    $stSql .= " FROM                                                            \n";
    $stSql .= "     contabilidade.encerramentoAnualLancamentosCompensado(      \n";
    $stSql .= "   '".$this->getDado("stExercicio")."','".$this->getDado("inCodEntidade")."')\n";

    return $stSql;
}

function gerarEncerramentoResultadoApurado(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaGerarEncerramentoResultadoApurado();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaGerarEncerramentoResultadoApurado()
{
    $stSql  = " SELECT                                                          \n";
    $stSql .= "     *                                                           \n";
    $stSql .= " FROM                                                            \n";
    $stSql .= "     contabilidade.encerramentoAnualLancamentosResultadoApurado( \n";
    $stSql .= "   '".$this->getDado("stExercicio")."','".$this->getDado("inCodEntidade")."')\n";

    return $stSql;
}

function fezEncerramentoReceita(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaFezEncerramentoReceita();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaFezEncerramentoReceita()
{
    $stSql  = " SELECT                                                                              \n";
    $stSql .= "     contabilidade.fezEncerramentoAnualLancamentosReceita(                           \n";
    $stSql .= "   '".$this->getDado("stExercicio")."','".$this->getDado("inCodEntidade")."') as fez \n";

    return $stSql;
}

function fezEncerramentoDespesa(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaFezEncerramentoDespesa();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaFezEncerramentoDespesa()
{
    $stSql  = " SELECT                                                                              \n";
    $stSql .= "     contabilidade.fezEncerramentoAnualLancamentosDespesa(                           \n";
    $stSql .= "   '".$this->getDado("stExercicio")."','".$this->getDado("inCodEntidade")."') as fez \n";

    return $stSql;
}

function fezEncerramentoVariacoesPatri(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaFezEncerramentoVariacoesPatri();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaFezEncerramentoVariacoesPatri()
{
    $stSql  = " SELECT                                                                              \n";
    $stSql .= "     contabilidade.fezEncerramentoAnualLancamentosVariacoesPatri(                    \n";
    $stSql .= "   '".$this->getDado("stExercicio")."','".$this->getDado("inCodEntidade")."') as fez \n";

    return $stSql;
}

function fezEncerramentoOrcamentario(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaFezEncerramentoOrcamentario();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaFezEncerramentoOrcamentario()
{
    $stSql  = " SELECT                                                                              \n";
    $stSql .= "     contabilidade.fezEncerramentoAnualLancamentosCompensado(                        \n";
    $stSql .= "   '".$this->getDado("stExercicio")."','".$this->getDado("inCodEntidade")."') as fez \n";

    return $stSql;
}

function fezEncerramentoResultadoApurado(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaFezEncerramentoResultadoApurado();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaFezEncerramentoResultadoApurado()
{
    $stSql  = " SELECT                                                                              \n";
    $stSql .= "     contabilidade.fezEncerramentoAnualLancamentosResultadoApurado(                  \n";
    $stSql .= "   '".$this->getDado("stExercicio")."','".$this->getDado("inCodEntidade")."') as fez \n";

    return $stSql;
}

function excluiEncerramentoReceita(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaExcluiEncerramentoReceita();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaExcluiEncerramentoReceita()
{
    $stSql  = " SELECT                                                                              \n";
    $stSql .= "     contabilidade.ExcluiEncerramentoAnualLancamentosReceita(                        \n";
    $stSql .= "   '".$this->getDado("stExercicio")."','".$this->getDado("inCodEntidade")."') as fez \n";

    return $stSql;
}

function excluiEncerramentoDespesa(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaExcluiEncerramentoDespesa();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaExcluiEncerramentoDespesa()
{
    $stSql  = " SELECT                                                                              \n";
    $stSql .= "     contabilidade.excluiEncerramentoAnualLancamentosDespesa(                        \n";
    $stSql .= "   '".$this->getDado("stExercicio")."','".$this->getDado("inCodEntidade")."') as fez \n";

    return $stSql;
}

function excluiEncerramentoVariacoesPatri(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaExcluiEncerramentoVariacoesPatri();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaExcluiEncerramentoVariacoesPatri()
{
    $stSql  = " SELECT                                                                              \n";
    $stSql .= "     contabilidade.excluiEncerramentoAnualLancamentosVariacoesPatri(                 \n";
    $stSql .= "   '".$this->getDado("stExercicio")."','".$this->getDado("inCodEntidade")."') as fez \n";

    return $stSql;
}

function excluiEncerramentoOrcamentario(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaExcluiEncerramentoOrcamentario();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaExcluiEncerramentoOrcamentario()
{
    $stSql  = " SELECT                                                                              \n";
    $stSql .= "     contabilidade.excluiEncerramentoAnualLancamentosCompensado(                     \n";
    $stSql .= "   '".$this->getDado("stExercicio")."','".$this->getDado("inCodEntidade")."') as fez \n";

    return $stSql;
}

function excluiSaldosBalanco(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaExcluiSaldosBalanco();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaExcluiSaldosBalanco()
{
    $stSql  = " SELECT                                                                              \n";
    $stSql .= "     contabilidade.excluiSaldosBalanco(                     \n";
    $stSql .= "   '".($this->getDado("stExercicio")+1)."','".$this->getDado("inCodEntidade")."') as fez \n";

    return $stSql;
}

function excluiEncerramentoResultadoApurado(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaExcluiEncerramentoResultadoApurado();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaExcluiEncerramentoResultadoApurado()
{
    $stSql  = " SELECT                                                                              \n";
    $stSql .= "     contabilidade.ExcluiEncerramentoAnualLancamentosResultadoApurado(               \n";
    $stSql .= "   '".$this->getDado("stExercicio")."','".$this->getDado("inCodEntidade")."') as fez \n";

    return $stSql;
}

    public function gerarRestosPagarDestinacaoRecurso(&$rsRecordSet,$boTransacao="")
    {
        $stSql = "
            SELECT *
              FROM contabilidade.fn_gerar_restos_pagar_destinacao_recurso('" . $this->getDado('stExercicio') . "')";

        return $this->executaRecuperaSql($stSql,$rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    }

    /*
     * Validação dos lançamento que começam a funcionar apartir de 2013
     */
    public function fezEncerramentoVariacoesPatrimoniais2013(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaFezEncerramentoVariacoesPatrimoniais2013();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaFezEncerramentoVariacoesPatrimoniais2013()
    {
        $stSql  = " SELECT                                                                              \n";
        $stSql .= "     contabilidade.fezEncerramentoAnualLancamentosVariacoesPatrimoniais2013(         \n";
        $stSql .= "   '".$this->getDado("stExercicio")."','".$this->getDado("inCodEntidade")."') as fez \n";

        return $stSql;
    }

    public function fezEncerramentoOrcamentario2013(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaFezEncerramentoOrcamentario2013();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaFezEncerramentoOrcamentario2013()
    {
        $stSql  = " SELECT                                                                              \n";
        $stSql .= "     contabilidade.fezEncerramentoAnualLancamentosOrcamentario2013(                  \n";
        $stSql .= "   '".$this->getDado("stExercicio")."','".$this->getDado("inCodEntidade")."') as fez \n";

        return $stSql;
    }

    public function fezEncerramentoControle2013(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaFezEncerramentoControle2013();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaFezEncerramentoControle2013()
    {
        $stSql  = " SELECT                                                                              \n";
        $stSql .= "     contabilidade.fezEncerramentoAnualLancamentosControle2013(                      \n";
        $stSql .= "   '".$this->getDado("stExercicio")."','".$this->getDado("inCodEntidade")."') as fez \n";

        return $stSql;
    }

    /*
     * Fim das validações que funcionam apartir de 2013
     */

    /*
     * Inicio da geração de Encerramento que funciona apartir de 2013
     */
    public function gerarEncerramentoVariacoes2013(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaGerarEncerramentoVariacoes2013();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaGerarEncerramentoVariacoes2013()
    {
        $stSql  = " SELECT                                                                        \n";
        $stSql .= "     *                                                                         \n";
        $stSql .= " FROM                                                                          \n";
        $stSql .= "     contabilidade.encerramentoAnualLancamentosVariacoesPatrimoniais2013(      \n";
        $stSql .= "   '".$this->getDado("stExercicio")."','".$this->getDado("inCodEntidade")."')  \n";

        return $stSql;
    }

    public function gerarEncerramentoOrcamentario2013(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaGerarEncerramentoOrcamentario2013();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaGerarEncerramentoOrcamentario2013()
    {
        $stSql  = " SELECT                                                                       \n";
        $stSql .= "     *                                                                        \n";
        $stSql .= " FROM                                                                         \n";
        $stSql .= "     contabilidade.encerramentoAnualLancamentosOrcamentario2013(              \n";
        $stSql .= "   '".$this->getDado("stExercicio")."','".$this->getDado("inCodEntidade")."') \n";

        return $stSql;
    }

    public function gerarEncerramentoControle2013(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaGerarEncerramentoControle2013();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaGerarEncerramentoControle2013()
    {
        $stSql  = " SELECT                                                                       \n";
        $stSql .= "     *                                                                        \n";
        $stSql .= " FROM                                                                         \n";
        $stSql .= "     contabilidade.encerramentoAnualLancamentosControle2013(                  \n";
        $stSql .= "   '".$this->getDado("stExercicio")."','".$this->getDado("inCodEntidade")."') \n";

        return $stSql;
    }

    /*
     * Fim da geração de Encerramento que funciona apartir de 2013
     */

    /*
     * Inicio da exclusão da geração de Encerramento que funciona apartir de 2013
     */
    public function excluiEncerramentoVariacoesPatrimoniais2013(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaExcluiEncerramentoVariacoesPatrimoniais2013();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaExcluiEncerramentoVariacoesPatrimoniais2013()
    {
        $stSql  = " SELECT                                                                              \n";
        $stSql .= "     contabilidade.excluiEncerramentoAnualLancamentosVariacoesPatrimoniais2013(                 \n";
        $stSql .= "   '".$this->getDado("stExercicio")."','".$this->getDado("inCodEntidade")."') as fez \n";

        return $stSql;
    }

    public function excluiEncerramentoVariacoesOrcamentario2013(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaExcluiEncerramentoOrcamentario2013();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaExcluiEncerramentoOrcamentario2013()
    {
        $stSql  = " SELECT                                                                              \n";
        $stSql .= "     contabilidade.excluiEncerramentoAnualLancamentosOrcamentario2013(                 \n";
        $stSql .= "   '".$this->getDado("stExercicio")."','".$this->getDado("inCodEntidade")."') as fez \n";

        return $stSql;
    }

    public function excluiEncerramentoControle2013(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaExcluiEncerramentoControle2013();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaExcluiEncerramentoControle2013()
    {
        $stSql  = " SELECT                                                                              \n";
        $stSql .= "     contabilidade.excluiEncerramentoAnualLancamentosControle2013(                   \n";
        $stSql .= "   '".$this->getDado("stExercicio")."','".$this->getDado("inCodEntidade")."') as fez \n";

        return $stSql;
    }
    /*
     * Fim da exclusão geração de Encerramento que funciona apartir de 2013
     */
}

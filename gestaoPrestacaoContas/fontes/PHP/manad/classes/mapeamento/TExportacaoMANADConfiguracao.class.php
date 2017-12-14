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
    * Classe de mapeamento da Tabela
    * Data de Criação: 14/11/2012
    *
    * @author Analista: Gelson Gonçalves
    * @author Desenvolvedor: Matheus Figueredo
    *
    * @package URBEM
    * @subpackage Mapeamento
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once (CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php");

class TExportacaoMANADConfiguracao extends TAdministracaoConfiguracao
{
    /**
     * Método Construtor
     * @access Private
     */
    public function TExportacaoMANADConfiguracao()
    {
        parent::TAdministracaoConfiguracao();

        $this->SetDado("exercicio", Sessao::getExercicio());
        $this->SetDado("cod_modulo", 59);
    }

    public function recuperaConfiguracao(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro();
        $obConexao   = new Conexao();
        $rsRecordSet = new RecordSet();

        $stSql = $this->montaRecuperaConfiguracao();
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);
    }

    public function MontaRecuperaConfiguracao()
    {
        $stSql  ="   SELECT parametro                                                       \n";
        $stSql  .="    FROM administracao.configuracao                             \n";
        $stSql  .="  WHERE cod_modulo= 8                                                \n";
        $stSql  .="       AND valor= '".$this->getDado('valor')."'                  \n";
        $stSql  .="       AND exercicio= '".$this->getDado('stExercicio')."'                  \n";

        return $stSql;
    }

}

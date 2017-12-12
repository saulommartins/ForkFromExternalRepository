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
    * Classe de mapeamento da tabela
    * Data de Criação: 11/07/2006

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Anderson C. Konze

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TExportacaoTCERSConfiguracao.class.php 57368 2014-02-28 17:23:28Z diogo.zarpelon $

    * Casos de uso: uc-02.08.15

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracaoEntidade.class.php");

class TTCEALExportacaoConfiguracao extends TAdministracaoConfiguracaoEntidade
{

/**
    * Método Construtor
    * @access Private
*/
    public function __construct()
    {
        parent::TAdministracaoConfiguracaoEntidade();

        $this->SetDado("exercicio",Sessao::getExercicio());
        $this->SetDado("cod_modulo",62);
    }
    
    function recuperaOrgaoUnidade(&$rsRecordSet, $stFiltro = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        $stSql = $this->montaRecuperaOrgaoUnidade().$stFiltro.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    function montaRecuperaOrgaoUnidade()
    {
        $stSql = "
                    SELECT configuracao_entidade.cod_entidade
                         , sw_cgm.nom_cgm
                         , configuracao_entidade.valor

                      FROM administracao.configuracao_entidade

                INNER JOIN orcamento.entidade
                        ON entidade.cod_entidade = configuracao_entidade.cod_entidade
                       AND entidade.exercicio = configuracao_entidade.exercicio

                INNER JOIN sw_cgm
                        ON sw_cgm.numcgm = entidade.numcgm

                     WHERE configuracao_entidade.parametro ilike 'tceal_orgao%'
                       AND configuracao_entidade.exercicio = '".Sessao::getExercicio()."'
                ";
    
        return $stSql;
    }

    function recuperaEntidades(&$rsRecordSet, $stFiltro = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        $stSql = $this->montaRecuperaEntidades().$stFiltro.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    function montaRecuperaEntidades()
    {
        $stSql = "
                    SELECT entidade.cod_entidade
                         , sw_cgm.nom_cgm

                      FROM orcamento.entidade

                INNER JOIN sw_cgm
                        ON sw_cgm.numcgm = entidade.numcgm

                     WHERE entidade.exercicio = '".Sessao::getExercicio()."'
                ";
    
        return $stSql;
    }
}
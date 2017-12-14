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
    * Classe Mapeamento da Tabela
    *
    *
    *
    * @author Tonismar R. Bernardo
    * @date 04/03/2011
    *
    * @package URBEM
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once (CAM_GA_ADM_MAPEAMENTO.'TAdministracaoConfiguracaoEntidade.class.php');

class TTCEAMConfiguracaoEntidade extends TAdministracaoConfiguracaoEntidade
{
    /**
    * Método Construtor
    * @access Private
    */
    public function TTCEAMConfiguracaoEntidade()
    {
        parent::TAdministracaoConfiguracaoEntidade();
        $this->setDado('exercicio', Sessao::getExercicio());
        $this->setDado('cod_modulo',56); /* confirmar número gerado Fábio */
    }

    public function recuperaCodigos(&$rsRecordSet, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        $obErro = new Erro;
        $obConexao = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaCodigos().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, '', $boTransacao );
    }

    public function montaRecuperaCodigos()
    {
        $stSql = " SELECT  entidade.cod_entidade
                          ,sw_cgm.nom_cgm
                          ,configuracao_entidade.valor
                     FROM sw_cgm
                     JOIN orcamento.entidade
                       ON sw_cgm.numcgm = entidade.numcgm
                LEFT JOIN administracao.configuracao_entidade
                       ON entidade.exercicio = configuracao_entidade.exercicio
                      AND entidade.cod_entidade = configuracao_entidade.cod_entidade
                      AND configuracao_entidade.cod_modulo = '".$this->getDado('cod_modulo')."'
                      AND configuracao_entidade.parametro = 'tceam_codigo_unidade_gestora'
                    WHERE entidade.exercicio = '".$this->getDado('exercicio')."' ";

        return $stSql;
    }
}
?>

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
    * Classe de mapeamento da tabela folhapagamento.bases
    * Data de Criação: 18/08/2008

    * @author Analista: Dagiane
    * @author Desenvolvedor: Rafael Garbin

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-04.05.68

    $Id: TFolhaPagamentoBases.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TFolhaPagamentoBases extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TFolhaPagamentoBases()
    {
        parent::Persistente();
        $this->setTabela("folhapagamento.bases");

        $this->setCampoCod('cod_base');
        $this->setComplementoChave('');

        $this->AddCampo('cod_base'           ,'sequence',true  ,''    ,true,false);
        $this->AddCampo('nom_base'           ,'varchar' ,true  ,'30'  ,false,false);
        $this->AddCampo('tipo_base'          ,'char'    ,true  ,'1'   ,false,false);
        $this->AddCampo('apresentacao_valor' ,'boolean' ,true  ,''    ,false,false);
        $this->AddCampo('insercao_automatica','boolean' ,true  ,''    ,false,false);
        $this->AddCampo('cod_modulo'         ,'integer' ,true  ,''    ,false,'TAdministracaoFuncao');
        $this->AddCampo('cod_biblioteca'     ,'integer' ,true  ,''    ,false,'TAdministracaoFuncao');
        $this->AddCampo('cod_funcao'         ,'integer' ,true  ,''    ,false,'TAdministracaoFuncao');
    }

    public function montaRecuperaEntidadeFuncao()
    {
        $stSql  = " SELECT sw_cgm.nom_cgm                                            \n";
        $stSql .= "   FROM administracao.funcao                                      \n";
        $stSql .= "     , administracao.biblioteca_entidade                          \n";
        $stSql .= "     , orcamento.entidade                                         \n";
        $stSql .= "     , sw_cgm                                                     \n";
        $stSql .= " WHERE funcao.cod_modulo = biblioteca_entidade.cod_modulo         \n";
        $stSql .= "   and funcao.cod_biblioteca = biblioteca_entidade.cod_biblioteca \n";
        $stSql .= "   and biblioteca_entidade.cod_entidade = entidade.cod_entidade   \n";
        $stSql .= "   and biblioteca_entidade.exercicio = entidade.exercicio         \n";
        $stSql .= "   and entidade.numcgm = sw_cgm.numcgm                            \n";

        return $stSql;
    }

    public function recuperaEntidadeFuncao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaEntidadeFuncao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function executaFuncaoPL($stDML, $boTransacao = "")
    {
        $obErro     = new Erro;
        $obConexao  = new Transacao;

        $obErro = $obConexao->executaDML( $stDML, $boTransacao );

        return $obErro;
    }

}
?>

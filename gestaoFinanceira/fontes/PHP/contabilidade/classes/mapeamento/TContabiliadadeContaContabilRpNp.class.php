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

    $Id: $

    * Casos de uso: uc-02.08.15

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TContabiliadadeContaContabilRpNp extends Persistente
    {

    /**
        * Método Construtor
        * @access Private
    */
    public function TContabiliadadeContaContabilRpNp()
    {
        parent::Persistente();

        $this->setTabela('contabilidade.conta_contabil_rp_np');
        $this->setComplementoChave('exercicio,cod_conta');

        $this->AddCampo('exercicio'     ,'varchar',true,'4',true,true);
        $this->AddCampo('cod_conta'     ,'integer',true,'' ,true,true);
        $this->AddCampo('cod_entidade'  ,'integer',true,'' ,false,true);

        $this->SetDado("exercicio",Sessao::getExercicio());
    }

    public function montaRecuperaRelacionamento()
    {
        $stSql = "
        SELECT   conta_contabil_rp_np.cod_entidade
                ,cgm.nom_cgm
                ,plc.cod_estrutural
                ,plc.cod_conta
                ,plc.nom_conta
                ,plc.exercicio
        FROM     contabilidade.conta_contabil_rp_np
                ,orcamento.entidade         as ore
                ,sw_cgm                     as cgm
                ,contabilidade.plano_conta  as plc
        WHERE   conta_contabil_rp_np.exercicio       = ore.exercicio
        AND     conta_contabil_rp_np.cod_entidade    = ore.cod_entidade
        AND     ore.numcgm          = cgm.numcgm
        AND     conta_contabil_rp_np.exercicio       = plc.exercicio
        AND     conta_contabil_rp_np.cod_conta       = plc.cod_conta
        ".( $this->getDado('exercicio') ? " AND     plc.exercicio       = '".$this->getDado('exercicio')."'" : '' )."
        ORDER BY conta_contabil_rp_np.cod_entidade, plc.cod_estrutural
        ";

        return $stSql;

    }

    public function recuperaNomeEntidade(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaNomeEntidade();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaNomeEntidade()
    {
        $stSql = "
                SELECT nom_cgm
                  FROM     contabilidade.conta_contabil_rp_np
                      , orcamento.entidade         as ore
                      , sw_cgm                     as cgm
                      , contabilidade.plano_conta  as plc
                  WHERE conta_contabil_rp_np.exercicio       = ore.exercicio
                    ANDconta_contabil_rp_np.cod_entidade    = ore.cod_entidade
                    AND ore.numcgm          = cgm.numcgm
                    AND conta_contabil_rp_np.exercicio       = plc.exercicio
                    ANDconta_contabil_rp_np.cod_conta       = plc.cod_conta
     ".( $this->getDado('exercicio')     ? " AND plc.exercicio    = '".$this->getDado('exercicio')."'": '' )."
     ".( $this->getDado('inCodEntidade') ? " AND ore.cod_entidade = ".$this->getDado('inCodEntidade') : '' )."
                  GROUP BY nom_cgm
     ";

    return $stSql;

    }

}

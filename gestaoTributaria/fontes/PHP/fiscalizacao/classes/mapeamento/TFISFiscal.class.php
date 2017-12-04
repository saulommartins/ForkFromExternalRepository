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
    * Classe de regra de mapeamento para FISCALIZACAO.FISCAL
    * Data de Criacao: 17/07/2007

    * @author Analista      : Fábio Bertoldi Rodrigues
    * @author Desenvolvedor : Rodrigo D.S.
    * @ignore

    * $Id: TFiscal.class.php 30552 2008-06-26 17:22:38Z domluc $

    *Casos de uso: uc-05.07.02

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE 									                                   );

class TFISFiscal extends Persistente
{
    /**
        * Metodo Construtor
        * @access Private
    */

    public function TFISFiscal()
    {
        parent::Persistente();
        $this->setTabela('fiscalizacao.fiscal');

        $this->setCampoCod('cod_fiscal');
        $this->setComplementoChave('cod_contrato,numcgm');

        $this->AddCampo( 'cod_fiscal','integer',true,'',true,false    );
        $this->AddCampo( 'numcgm','integer',true,'',true,false        );
        $this->AddCampo( 'cod_contrato','integer',true,'',false,false );
        $this->AddCampo( 'administrador','boolean',true,'',false,false);
        $this->AddCampo( 'ativo','boolean',true,'',false,false        );
    }

    public function recuperaListaFiscal(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaFiscal($stCondicao).$stOrdem;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaFiscal($criterio)
    {
        $stSql = "SELECT fc.cod_fiscal AS codigo,
                       gl.nom_cgm AS nome,
                     fc.numcgm AS numero,
                       fc.cod_contrato AS contrato,
                      fc.administrador AS adm,
                      fc.ativo
                                  FROM fiscalizacao.fiscal fc
                            INNER JOIN sw_cgm_pessoa_fisica pf
                                    ON fc.numcgm = pf.numcgm
                            INNER JOIN sw_cgm gl
                                    ON pf.numcgm = gl.numcgm
                            INNER JOIN pessoal.contrato ct
                                    ON fc.cod_contrato = ct.cod_contrato ".$criterio;

        return $stSql;
    }

    public function recuperaListaFiscais(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaFiscais($stCondicao).$stOrdem;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaFiscais($stCondicao)
    {
         $stSql =" SELECT fiscal.cod_fiscal                                          \n";
         $stSql.="      , fiscal.numcgm                                              \n";
         $stSql.="      , fiscal.cod_contrato                                        \n";
         $stSql.="      , fiscal.administrador                                       \n";
         $stSql.="      , CASE fiscal.ativo                                          \n";
         $stSql.="           WHEN true  THEN 'Ativo'                                 \n";
         $stSql.="            WHEN false THEN 'Inativo'                              \n";
         $stSql.="        END AS ativo                                               \n";
         $stSql.="      , sw_cgm.nom_cgm                                             \n";
         $stSql.="      , contrato.registro                                          \n";
         $stSql.="   FROM fiscalizacao.fiscal                                        \n";
         $stSql.="      , sw_cgm_pessoa_fisica                                       \n";
         $stSql.="      , sw_cgm                                                     \n";
         $stSql.="      , pessoal.contrato                                           \n";
         $stSql.="  WHERE fiscal.numcgm               = sw_cgm_pessoa_fisica.numcgm  \n";
         $stSql.="    AND sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm                \n";
         $stSql.="    AND contrato.cod_contrato       = fiscal.cod_contrato          \n";
        $stSql.= $stCondicao;

        return $stSql;
    }

    /**
     * Método que retorna todos os Funcionários que não estão Demitidos,
     * ou Aposentados ou Pensionistas
     *
     * @access Public
     * @return recordSet
     *
     */
    public function recuperaContratoFiscais(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaContratoFiscais($stCondicao).$stOrdem;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    /*
     * @access Private
     * @return String
     */
    public function montaContratoFiscais($stCondicao)
    {
        $stSql ="    SELECT pc.cod_contrato                                                 \n";
         $stSql.="	      , pc.registro                                                     \n";
         $stSql.="         , pscs.cod_servidor                                               \n";
         $stSql.="         , cgm.numcgm                                                      \n";
         $stSql.="         , cgm.nom_cgm                                                     \n";
         $stSql.="      FROM pessoal".Sessao::getEntidade().".contrato AS pc                 \n";
         $stSql.="INNER JOIN pessoal".Sessao::getEntidade().".contrato_servidor AS pcs       \n";
         $stSql.="	     ON pcs.cod_contrato = pc.cod_contrato                              \n";
         $stSql.="INNER JOIN pessoal".Sessao::getEntidade().".servidor_contrato_servidor AS pscs \n";
         $stSql.="	     ON pscs.cod_contrato = pcs.cod_contrato                            \n";
         $stSql.="INNER JOIN pessoal".Sessao::getEntidade().".servidor AS ps                 \n";
         $stSql.="	     ON ps.cod_servidor = pscs.cod_servidor                             \n";
         $stSql.="INNER JOIN sw_cgm_pessoa_fisica AS cgm_f                                   \n";
         $stSql.="	     ON cgm_f.numcgm = ps.numcgm                                        \n";
         $stSql.="INNER JOIN sw_cgm AS cgm                                                   \n";
         $stSql.="	     ON cgm.numcgm = cgm_f.numcgm                                       \n";
         $stSql.=" LEFT JOIN pessoal".Sessao::getEntidade().".contrato_servidor_caso_causa AS pcscc \n";
         $stSql.="	     ON pcscc.cod_contrato = pcs.cod_contrato                           \n";
         $stSql.=" LEFT JOIN pessoal".Sessao::getEntidade().".contrato_pensionista AS pcp    \n";
         $stSql.="	     ON pcp.cod_contrato = pc.cod_contrato                              \n";
         $stSql.=" LEFT JOIN pessoal".Sessao::getEntidade().".aposentadoria AS pa            \n";
         $stSql.="	     ON pa.cod_contrato = pcs.cod_contrato                              \n";
         $stSql.=" LEFT JOIN pessoal".Sessao::getEntidade().".aposentadoria_excluida AS pae  \n";
         $stSql.="	     ON pae.cod_contrato = pa.cod_contrato                              \n";
         $stSql.="       AND pae.timestamp_aposentadoria = pa.timestamp                      \n";

         $stOrder.="ORDER BY cgm.nom_cgm ASC                                                 \n";

        $stSql.= $stCondicao . $stOrder;

        return $stSql;
    }
}// fecha classe de mapeamento

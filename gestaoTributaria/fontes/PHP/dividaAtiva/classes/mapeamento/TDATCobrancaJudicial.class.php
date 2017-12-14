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
    * Classe de mapeamento da tabela DIVIDA.COBRANCA_JUDICIAL
    * Data de Criação: 11/09/2007

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TDATCobrancaJudicial.class.php 59612 2014-09-02 12:00:51Z gelson $s

* Casos de uso: uc-05.04.02
*/

/*
$Log$
Revision 1.1  2007/09/11 20:43:08  cercato
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
//include_once    ( CLA_PERSISTENTE );

class TDATCobrancaJudicial extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TDATCobrancaJudicial()
    {
        parent::Persistente();
        $this->setTabela('divida.cobranca_judicial');

        $this->setCampoCod('');
        $this->setComplementoChave('');

        $this->AddCampo('cod_inscricao', 'integer', true, '', true, true);
        $this->AddCampo('exercicio', 'varchar', true, '4', true, true);
        $this->AddCampo('numcgm', 'integer', false, '', false, true);
        $this->AddCampo('timestamp', 'timestamp', false, '', false, false);
    }

    public function recuperaListaInscricoes(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaInscricoes().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        //$this->debug();
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaInscricoes()
    {
        $stSql  = " SELECT
                        ddc.numcgm,
                        (
                            SELECT
                                sw_cgm.nom_cgm
                            FROM
                                sw_cgm
                            WHERE
                                sw_cgm.numcgm = ddc.numcgm
                        )AS nom_cgm,
                        (
                            SELECT
                                min(divida_parcelamento.num_parcelamento)
                            FROM
                                divida.divida_parcelamento
                            WHERE
                                divida_parcelamento.cod_inscricao = dda.cod_inscricao
                                AND divida_parcelamento.exercicio = dda.exercicio
                        )AS num_parcelamento,
                        dda.cod_inscricao,
                        dda.exercicio,
                        to_char(dda.dt_inscricao, 'dd/mm/yyyy') AS dt_inscricao_divida

                    FROM
                        divida.divida_ativa AS dda

                    INNER JOIN
                        divida.divida_cgm AS ddc
                    ON
                        dda.cod_inscricao = ddc.cod_inscricao
                        AND dda.exercicio = ddc.exercicio

                    LEFT JOIN
                        divida.divida_imovel AS ddi
                    ON
                        ddi.cod_inscricao = ddc.cod_inscricao
                        AND ddi.exercicio = ddc.exercicio

                    LEFT JOIN
                        divida.divida_empresa AS dde
                    ON
                        dde.cod_inscricao = ddc.cod_inscricao
                        AND dde.exercicio = ddc.exercicio \n";

        return $stSql;
    }

    public function recuperaListaDocumentos(&$rsRecordSet, $stAcao, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaDocumentos( $stAcao );
        $this->setDebug( $stSql );
        //$this->debug();
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaDocumentos($stAcao)
    {
        $stSql  = " SELECT
                        modelo_arquivos_documento.cod_documento,
                        modelo_arquivos_documento.cod_tipo_documento

                    FROM
                        administracao.modelo_arquivos_documento

                    WHERE
                        modelo_arquivos_documento.cod_acao = ".$stAcao;

        return $stSql;
    }

}// end of class

?>

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
    * Classe de mapeamento da tabela DIVIDA.DIVIDA_PARCELAMENTO
    * Data de Criação: 25/09/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TDATDividaParcelamento.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.04.02
*/

/*
$Log$
Revision 1.9  2007/08/08 19:09:18  cercato
correcao na consulta da popup de cobranca.

Revision 1.8  2007/07/19 21:00:47  cercato
Bug #9705#

Revision 1.7  2007/07/13 15:39:32  cercato
correcao para funcionar a lista de cobrancas

Revision 1.6  2007/04/16 18:09:01  cercato
adicionando funcoes para emitir carne pela divida.

Revision 1.5  2007/02/09 18:27:49  cercato
correcoes para divida.cobranca

Revision 1.4  2006/10/06 17:04:04  dibueno
inserção das chaves da tabela

Revision 1.3  2006/10/05 14:54:03  dibueno
Alterações nas colunas da tabela

Revision 1.2  2006/09/29 17:17:45  dibueno
Inclusao de função para retornar numero do ultimo parcelamento

Revision 1.1  2006/09/25 14:54:56  cercato
classes de mapeamento para funcionamento da modalidade.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
//include_once    ( CLA_PERSISTENTE );

class TDATDividaParcelamento extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TDATDividaParcelamento()
    {
        parent::Persistente();
        $this->setTabela('divida.divida_parcelamento');

        $this->setCampoCod('');
        $this->setComplementoChave('exercicio, cod_inscricao');

        $this->AddCampo('exercicio','varchar',true,'4',true,true);
        $this->AddCampo('cod_inscricao','integer',true,'',true,true);
        $this->AddCampo('num_parcelamento','integer',true,'',true,true);
    }

    public function recuperaCodigoCobrancaComponente(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaCodigoCobrancaComponente();
        $this->setDebug( $stSql );
        //$this->debug();
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaCodigoCobrancaComponente()
    {
        $stSql  = " SELECT \n";
        $stSql .= "     MAX(numero_parcelamento) AS max_inscricao \n";
        $stSql .= " FROM \n";
        $stSql .= "     divida.parcelamento \n";

        return $stSql;
    }

    public function recuperaListaCobrancaPopUP(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaCobrancaPopUP().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );

        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaCobrancaPopUP()
    {
        $stSql  = "
       SELECT divida_ativa.cod_inscricao
            , divida_ativa.exercicio
            , divida_cgm.numcgm
            , sw_cgm.nom_cgm
            , COALESCE( divida_imovel.inscricao_municipal, divida_empresa.inscricao_economica )||' - ' AS cod_inscricao_imec
            , CASE WHEN divida_imovel.inscricao_municipal IS NOT NULL THEN
                ( select split_part ( imobiliario.fn_busca_endereco_imovel( divida_imovel.inscricao_municipal ), '§', 1)||' '||split_part ( imobiliario.fn_busca_endereco_imovel( divida_imovel.inscricao_municipal ), '§', 3)||', '||split_part ( imobiliario.fn_busca_endereco_imovel( divida_imovel.inscricao_municipal ), '§', 4) )
              ELSE
                 divida_empresa.nom_cgm
              END AS descricao_inscricao_imec
            , CASE WHEN divida_imovel.cod_inscricao is not null then
                'IM'
              ELSE
                'IE'
              END as tipo_divida
            , CASE WHEN divida_cancelada.cod_inscricao is not null then
                true
              ELSE
                false
              END as cancelada
            , parcelamento.numero_parcelamento
            , parcelamento.exercicio AS exercicio_cobranca
         FROM divida.divida_ativa
   INNER JOIN divida.divida_cgm
           ON divida_cgm.cod_inscricao = divida_ativa.cod_inscricao
          AND divida_cgm.exercicio = divida_ativa.exercicio
   INNER JOIN sw_cgm
           ON sw_cgm.numcgm = divida_cgm.numcgm
    LEFT JOIN divida.divida_imovel
           ON divida_imovel.cod_inscricao = divida_ativa.cod_inscricao
          AND divida_imovel.exercicio = divida_ativa.exercicio
    LEFT JOIN ( SELECT divida_empresa.*
                     , sw_cgm.nom_cgm
                  FROM divida.divida_empresa
             LEFT JOIN economico.cadastro_economico_empresa_direito
                    ON cadastro_economico_empresa_direito.inscricao_economica = divida_empresa.inscricao_economica
             LEFT JOIN economico.cadastro_economico_empresa_fato
                    ON cadastro_economico_empresa_fato.inscricao_economica = divida_empresa.inscricao_economica
             LEFT JOIN economico.cadastro_economico_autonomo
                    ON cadastro_economico_autonomo.inscricao_economica = divida_empresa.inscricao_economica
            INNER JOIN sw_cgm
                    ON sw_cgm.numcgm = COALESCE( cadastro_economico_empresa_direito.numcgm, cadastro_economico_empresa_fato.numcgm, cadastro_economico_autonomo.numcgm )
              )AS divida_empresa
           ON divida_empresa.cod_inscricao = divida_ativa.cod_inscricao
          AND divida_empresa.exercicio = divida_ativa.exercicio
    LEFT JOIN divida.divida_cancelada
           ON divida_cancelada.cod_inscricao = divida_ativa.cod_inscricao
          AND divida_cancelada.exercicio = divida_ativa.exercicio
   INNER JOIN ( SELECT divida_parcelamento.cod_inscricao
                     , divida_parcelamento.exercicio
                     , max( divida_parcelamento.num_parcelamento ) AS num_parcelamento
                  FROM divida.divida_parcelamento
              GROUP BY divida_parcelamento.cod_inscricao
                     , divida_parcelamento.exercicio
              )AS divida_parcelamento
           ON divida_parcelamento.cod_inscricao = divida_ativa.cod_inscricao
          AND divida_parcelamento.exercicio = divida_ativa.exercicio
   INNER JOIN divida.parcelamento
           ON parcelamento.num_parcelamento = divida_parcelamento.num_parcelamento
           ";

        return $stSql;
    }

}// end of class

?>

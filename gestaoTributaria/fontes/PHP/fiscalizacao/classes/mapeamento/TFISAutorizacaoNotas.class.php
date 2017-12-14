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
    * Data de Criacao: 27/07/2007

    * @author Analista      : Fábio Bertoldi Rodrigues
    * @author Desenvolvedor : Rodrigo D.S.
    * @ignore

    * $Id: TAutorizacaoNotas.class.php 29237 2008-04-16 12:02:48Z fabio $

    *Casos de uso: uc-05.07.04

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE                                                                      );

class TFISAutorizacaoNotas extends Persistente
{
/**
    * Metodo Construtor
    * @access Private
*/
    public function TFISAutorizacaoNotas()
    {
        parent::Persistente();
        $this->setTabela('fiscalizacao.autorizacao_notas');

        $this->setCampoCod('cod_autorizacao');
        $this->setComplementoChave('numcgm,inscricao_economica,numcgm_usuario');

        $this->AddCampo( 'cod_autorizacao', 'integer', true, '', true, false );
        $this->AddCampo( 'numcgm','integer', true, '', true, false );
        $this->AddCampo( 'inscricao_economica', 'integer', true, '', false, false );
        $this->AddCampo( 'numcgm_usuario', 'integer', true, '', true, false );
        $this->AddCampo( 'serie', 'varchar', true, '10', true, false );
        $this->AddCampo( 'qtd_taloes', 'integer', true, '', true, false );
        $this->AddCampo( 'nota_inicial', 'integer', true, '', true, false );
        $this->AddCampo( 'nota_final', 'integer', true, '', true, false );
        $this->AddCampo( 'qtd_vias', 'integer', true, '', true, false );
        $this->AddCampo( 'observacao', 'text', true, '', false, false );
        $this->AddCampo( 'timestamp', 'timestamp', false, '', true, false );
   }

    public function recuperaListaAutorizacaoNotas(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaAutorizacaoNotas().$stCondicao.$stOrdem;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaAutorizacaoNotas()
    {
       $stSql ="SELECT autorizacao_notas.cod_autorizacao                                                              \n";
       $stSql.="      ,autorizacao_notas.numcgm                                                                       \n";
       $stSql.="      ,(autorizacao_notas.inscricao_economica||' - '||sw_cgm.nom_cgm) AS inscricao_economica          \n";
       $stSql.="      ,autorizacao_notas.numcgm_usuario                                                               \n";
       $stSql.="      ,autorizacao_notas.serie                                                                        \n";
       $stSql.="      ,autorizacao_notas.qtd_taloes                                                                   \n";
       $stSql.="      ,autorizacao_notas.nota_inicial                                                                 \n";
       $stSql.="      ,autorizacao_notas.nota_final                                                                   \n";
       $stSql.="      ,autorizacao_notas.qtd_vias                                                                     \n";
       $stSql.="      ,autorizacao_notas.observacao                                                                   \n";
       $stSql.="  FROM fiscalizacao.autorizacao_notas                                                                 \n";
       $stSql.="      ,economico.cadastro_economico                                                                   \n";
       $stSql.="      LEFT JOIN economico.cadastro_economico_empresa_fato                                             \n";
       $stSql.="      ON cadastro_economico.inscricao_economica = cadastro_economico_empresa_fato.inscricao_economica \n";
       $stSql.="      LEFT JOIN economico.cadastro_economico_autonomo                                                 \n";
       $stSql.="      ON cadastro_economico.inscricao_economica=cadastro_economico_autonomo.inscricao_economica       \n";
       $stSql.="      LEFT JOIN economico.cadastro_economico_empresa_direito                                          \n";
       $stSql.="      ON cadastro_economico.inscricao_economica=cadastro_economico_empresa_direito.inscricao_economica\n";
       $stSql.="      LEFT JOIN economico.baixa_cadastro_economico                                                    \n";
       $stSql.="      ON cadastro_economico.inscricao_economica=baixa_cadastro_economico.inscricao_economica          \n";
       $stSql.="      ,sw_cgm                                                                                         \n";
       $stSql.="WHERE COALESCE( cadastro_economico_empresa_fato.numcgm                                                \n";
       $stSql.="               ,cadastro_economico_autonomo.numcgm                                                    \n";
       $stSql.="               ,cadastro_economico_empresa_direito.numcgm ) = sw_cgm.numcgm                           \n";
       $stSql.="  AND autorizacao_notas.inscricao_economica                 = cadastro_economico.inscricao_economica  \n";
       $stSql.="  AND baixa_cadastro_economico.inscricao_economica is null                                            \n";
       $stSql.="  AND (autorizacao_notas.nota_final - (autorizacao_notas.nota_inicial-1)) >                           \n";
       $stSql.="       ( SELECT COUNT(1)                                                                              \n";
       $stSql.="           FROM fiscalizacao.baixa_autorizacao                                                        \n";
       $stSql.="               ,fiscalizacao.baixa_notas                                                              \n";
       $stSql.="          WHERE baixa_notas.cod_baixa             = baixa_autorizacao.cod_baixa                       \n";
       $stSql.="            AND baixa_autorizacao.cod_autorizacao = autorizacao_notas.cod_autorizacao)                \n";

       return $stSql;
    }

}// Fecha classe de mapeamento

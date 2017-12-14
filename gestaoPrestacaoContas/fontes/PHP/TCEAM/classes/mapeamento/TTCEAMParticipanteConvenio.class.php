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
    * Extensão da Classe de Mapeamento
    * Data de Criação: 15/07/2014
    *
    *
    * @author: Lisiane Morais
    *
    $Id: TTCEAMParticipanteConvenio.class.php 59815 2014-09-12 17:15:45Z arthur $
    *
    * @package URBEM
    *
*/
class TTCEAMParticipanteConvenio extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEAMParticipanteConvenio()
    {
        parent::Persistente();
        $this->setDado('exercicio',Sessao::getExercicio());
    }

    public function recuperaParticipanteConvenio(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaParticipanteConvenio().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaParticipanteConvenio(){
      
        $stSql ="   SELECT *
                      FROM tceam.recupera_participante_convenio('".$this->getDado('exercicio')."','".$this->getDado('mes')."') 
                        AS retorno (  cod_cic_psticipante varchar
                               , tp_pessoa_participante integer
                               , nm_participante varchar
                               , vl_participacao text
                               , vl_percentual_participacao numeric
                               , nu_certidao_casan varchar
                               , data_certidao_casan varchar
                               , dt_validadecertidao_casan varchar
                               , nu_certidao_celesc varchar
                               , dt_certidao_celesc varchar
                               , dt_validade_certidao_celesc varchar
                               , nu_certidao_ipesc varchar
                               , dt_certidao_ipesc varchar 
                               , dt_validade_certidao_ipesc varchar
                               , nu_certidao_fazenda_municipal varchar
                               , dt_certidao_fazenda_municipal varchar
                               , dt_validade_certidao_fazenda_municipal varchar
                               , nu_certidao_fazenda_federal varchar
                               , dt_certidao_fazenda_federal varchar
                               , dt_validade_certidao_fazenda_federal varchar
                               , nu_certidao_cndt varchar
                               , dt_certidao_cndt varchar
                               , dt_validade_certidao_cndt varchar
                               , nu_certidao_outras varchar
                               , dt_certidao_outras varchar
                               , dt_validade_certidao_outras varchar
                               , num_convenio integer
                               , tp_esferaconvenio integer
                               , num_certificacao integer
                               , exercicio char(4)
                               , cgm_fornecedor integer
                              )";
        return $stSql;

    }

}
?>

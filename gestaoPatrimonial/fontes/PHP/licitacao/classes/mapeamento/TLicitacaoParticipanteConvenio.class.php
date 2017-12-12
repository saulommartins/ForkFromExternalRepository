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
    * Classe de mapeamento da tabela licitacao.participante_convenio
    * Data de Criação: 15/09/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Nome do Programador

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 26126 $
    $Name$
    $Author: girardi $
    $Date: 2007-10-16 17:23:35 -0200 (Ter, 16 Out 2007) $

    * Casos de uso: uc-03.05.14
*/
/*
$Log$
Revision 1.6  2007/10/16 19:23:35  girardi
adicionando ao repositório (inclusão, alteração e anulação de aditivos de convênio)

Revision 1.5  2007/09/18 15:14:03  bruce
Ticket#10098#

Revision 1.4  2007/09/05 21:59:08  leandro.zis
esfinge

Revision 1.3  2007/01/18 17:17:06  rodrigo
#8090#

Revision 1.2  2006/10/09 12:17:51  domluc
Caso de Uso : uc-03.05.14

Revision 1.1  2006/09/15 12:05:59  cleisson
inclusão

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  licitacao.participante_convenio
  * Data de Criação: 15/09/2006

  * @author Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Nome do Programador

  * @package URBEM
  * @subpackage Mapeamento
*/
class TLicitacaoParticipanteConvenio extends Persistente
{

    /**
        * Método Construtor
        * @access Private
    */
    public function TLicitacaoParticipanteConvenio()
    {
        parent::Persistente();
        $this->setTabela("licitacao.participante_convenio");

        $this->setCampoCod('');
        $this->setComplementoChave('exercicio,num_convenio,cgm_fornecedor,num_certificacao,exercicio_certificacao');

        $this->AddCampo('exercicio'              ,'char'   ,false ,'4'     ,true,'TLicitacaoConvenio');
        $this->AddCampo('num_convenio'           ,'integer',false ,''      ,true,'TLicitacaoConvenio');
        $this->AddCampo('num_certificacao'       ,'integer',false ,''      ,false,'TLicitacaoParticipanteCertificacao');
        $this->AddCampo('exercicio_certificacao' ,'char'   ,false ,'4'     ,false,'TLicitacaoParticipanteCertificacao','exercicio');
        $this->AddCampo('cgm_fornecedor'         ,'integer',false ,''      ,false,'TLicitacaoParticipanteCertificacao');
        $this->AddCampo('cod_tipo_participante'  ,'integer',false ,''      ,false,'TLicitacaoTipoParticipante');
        $this->AddCampo('valor_participacao'     ,'numeric',false ,'14,2'  ,false,false);
        $this->AddCampo('percentual_participacao','numeric',false ,'6,2'   ,false,false);
        $this->AddCampo('funcao'                 ,'char'   ,false ,'4', false, false    );

    }

    public function montaRecuperaRelacionamento()
    {
        $stSql = " select * from licitacao.participante_convenio ";

        return $stSql;
    }

    public function recuperaParticipanteConvenioEsfinge(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaParticipanteConvenioEsfinge",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaParticipanteConvenioEsfinge()
    {
        $stSql = "
                select convenio.num_convenio
                    ,sw_cgm_pessoa_juridica.cnpj
                    ,participante_convenio.cod_tipo_participante
                    ,sw_cgm.nom_cgm as nom_participante
                    ,participante_convenio.valor_participacao
                    ,participante_convenio.percentual_participacao
                from licitacao.convenio
                join licitacao.participante_convenio
                using (exercicio, num_convenio)
                join sw_cgm
                on sw_cgm.numcgm = participante_convenio.cgm_fornecedor
                join sw_cgm_pessoa_juridica
                on sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
                where convenio.dt_assinatura between to_date('".$this->getDado( 'dt_inicial')."','dd/mm/yyyy')
                and to_date('".$this->getDado( 'dt_final')."','dd/mm/yyyy')
                and convenio.exercicio = '".$this->getDado( 'exercicio')."'
                    ";
        return $stSql;
    }

    public function recuperaCertidaoConveniadoEsfinge(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaCertidaoConveniadoEsfinge",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaCertidaoConveniadoEsfinge()
    {
        $stSql = "
            select convenio.num_convenio
                ,sw_cgm_pessoa_juridica.cnpj
                ,tipo_certidao_esfinge.cod_tipo_certidao
                ,certificacao_documentos.num_documento
                ,to_char(certificacao_documentos.dt_emissao, 'dd/mm/yyyy') as dt_emissao
                ,to_char(certificacao_documentos.dt_validade, 'dd/mm/yyyy') as dt_validade
            from licitacao.convenio
            join licitacao.participante_convenio
            using (exercicio, num_convenio)
            join sw_cgm
            on sw_cgm.numcgm = participante_convenio.cgm_fornecedor
            join sw_cgm_pessoa_juridica
            on sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
            join licitacao.certificacao_documentos
            on participante_convenio.exercicio_certificacao = certificacao_documentos.exercicio
            and participante_convenio.num_certificacao = certificacao_documentos.num_certificacao
            and participante_convenio.cgm_fornecedor = certificacao_documentos.cgm_fornecedor
            join tcesc.tipo_certidao_esfinge
            on tipo_certidao_esfinge.cod_documento = certificacao_documentos.cod_documento
            where convenio.dt_assinatura between to_date('".$this->getDado( 'dt_inicial')."','dd/mm/yyyy')
            and to_date('".$this->getDado( 'dt_final')."','dd/mm/yyyy')
            and convenio.exercicio = '".$this->getDado( 'exercicio')."'
        ";
        return $stSql;
    }

    public function recuperaParticipanteConvenio(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaParticipanteConvenio",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaParticipanteConvenio()
    {
        $stSql = "SELECT  participante_convenio.cgm_fornecedor
                        , sw_cgm.nom_cgm
                  FROM licitacao.participante_convenio
                  INNER JOIN sw_cgm
                            ON sw_cgm.numcgm = participante_convenio.cgm_fornecedor
                  WHERE participante_convenio.num_convenio = ".$this->getDado( 'num_convenio')."
                  AND participante_convenio.exercicio = '".$this->getDado('exercicio')."'";
        return $stSql;
    }

}

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
    * Classe de mapeamento da tabela licitacao.contrato_documento
    * Data de Criação: 30/10/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Nome do Programador

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 28681 $
    $Name$
    $Author: luiz $
    $Date: 2008-03-20 16:21:04 -0300 (Qui, 20 Mar 2008) $

    * Casos de uso: uc-03.05.22
*/
/*
$Log$
Revision 1.5  2007/09/05 21:58:34  leandro.zis
esfinge

Revision 1.4  2006/11/22 21:29:57  leandro.zis
atualizado

Revision 1.3  2006/11/10 18:35:54  leandro.zis
atualizado para o caso de uso 03.05.22

Revision 1.2  2006/11/08 10:51:42  larocca
Inclusão dos Casos de Uso

Revision 1.1  2006/11/01 19:56:38  leandro.zis
atualizado

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  licitacao.contrato_documento
  * Data de Criação: 15/09/2006

  * @author Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Nome do Programador

  * @package URBEM
  * @subpackage Mapeamento
*/
class TLicitacaoContratoDocumento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/

function TLicitacaoContratoDocumento()
{
    parent::Persistente();
    $this->setTabela("licitacao.contrato_documento");

    $this->setCampoCod('');
    $this->setComplementoChave('num_contrato,exercicio,cod_entidade,cod_documento');

    $this->AddCampo('num_contrato'      ,'integer',true ,''   ,true,'TLicitacaoContrato');
    $this->AddCampo('exercicio'         ,'char'    ,true ,'4'  ,true,'TLicitacaoContrato');
    $this->AddCampo('cod_entidade' ,'integer',true,'',true,'TLicitacaoContrato');
    $this->AddCampo('cod_documento','integer',true,'',true,'TLicitacaoDocumento');
    $this->AddCampo('num_documento','varchar',true,'30',false,false);
    $this->AddCampo('dt_emissao',   'date'   ,true,'', false, false);
    $this->AddCampo('dt_validade',  'date'   ,false,'', false, false);
  }

function recuperaDocumentos(&$rsRecordSet, $stFiltro = "",$boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDocumentos();
//  echo "<pre>".$stSql."</pre>";
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDocumentos()
{
    $stSql = "SELECT contrato_documento.cod_documento                          \n";
    $stSql.= "      ,to_char(contrato_documento.dt_validade, 'dd/mm/yyyy') as dt_validade       \n";
    $stSql.= "      ,to_char(contrato_documento.dt_emissao, 'dd/mm/yyyy') as dt_emissao       \n";
    $stSql.= "      ,contrato_documento.num_documento                           \n";
    $stSql.= "      ,documento.nom_documento                                    \n";
    $stSql.= "  FROM licitacao.contrato_documento                     \n";
    $stSql.= "      ,licitacao.documento                              \n";
    $stSql.= " WHERE contrato_documento.cod_documento = documento.cod_documento           \n";
    $stSql.= "   and contrato_documento.num_contrato = ".$this->getDado('num_contrato')." \n";
    $stSql.= "   and contrato_documento.cod_entidade = ".$this->getDado('cod_entidade')." \n";
    $stSql.= "   and contrato_documento.exercicio = '".$this->getDado('exercicio')."'     \n";

    return $stSql;
}

function recuperaCertidaoContratadoEsfinge(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaCertidaoContratadoEsfinge",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaCertidaoContratadoEsfinge()
{
    $stSql = "
select contrato_documento.num_contrato
      ,tipo_certidao.cod_tipo_certidao
      ,contrato_documento.num_documento
      ,to_char(contrato_documento.dt_emissao, 'dd/mm/yyyy') as dt_emissao
      ,to_char(contrato_documento.dt_validade, 'dd/mm/yyyy') as dt_validade
from licitacao.contrato_documento
join licitacao.contrato
  on contrato.exercicio = contrato_documento.exercicio
 and contrato.cod_entidade = contrato_documento.cod_entidade
 and contrato.num_contrato = contrato_documento.num_contrato
join tcesc.tipo_certidao_esfinge
  on contrato_documento.cod_documento = tipo_certidao_esfinge.cod_documento
join tcesc.tipo_certidao
  on tipo_certidao_esfinge.cod_tipo_certidao = tipo_certidao.cod_tipo_certidao
where contrato.dt_assinatura between to_date('".$this->getDado( 'dt_inicial')."','dd/mm/yyyy')
  and to_date('".$this->getDado( 'dt_final')."','dd/mm/yyyy')
  and contrato.cod_entidade in (".$this->getDado( 'cod_entidade').")
  and contrato.exercicio = '".$this->getDado( 'exercicio')."'
    ";

    return $stSql;
}

}

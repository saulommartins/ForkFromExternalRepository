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
    * Classe de mapeamento da tabela licitacao.documentos_atributos
    * Data de Criação: 15/09/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Nome do Programador

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 22826 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-05-24 11:45:49 -0300 (Qui, 24 Mai 2007) $

    * Casos de uso: uc-03.05.12
*/
/*
$Log$
Revision 1.5  2007/05/24 14:45:49  hboaventura
Correção de bug

Revision 1.4  2006/11/08 10:51:42  larocca
Inclusão dos Casos de Uso

Revision 1.3  2006/10/06 13:30:25  leandro.zis
uc 03.05.12

Revision 1.2  2006/09/28 09:35:04  leandro.zis
ajustes conforme alteração da tabela de documentos_atributos

Revision 1.1  2006/09/15 12:05:59  cleisson
inclusão

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  licitacao.documentos_atributos
  * Data de Criação: 15/09/2006

  * @author Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Nome do Programador

  * @package URBEM
  * @subpackage Mapeamento
*/
class TLicitacaoDocumentosAtributos extends PersistenteAtributos
{
/**
    * Método Construtor
    * @access Private
*/
function TLicitacaoDocumentosAtributos()
{
    parent::PersistenteAtributos();
    $this->setTabela("licitacao.documentos_atributos");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_documento,cod_atributo,cod_cadastro,cod_modulo');

    $this->AddCampo('cod_documento','integer',true ,'',true,'TLicitacaoDocumento');
    $this->AddCampo('cod_atributo' ,'integer',true ,'',true,'TAdministracaoAtributoDinamico');
    $this->AddCampo('cod_cadastro' ,'integer',true ,'',true,'TAdministracaoAtributoDinamico');
    $this->AddCampo('cod_modulo'   ,'integer',true ,'',true,'TAdministracaoAtributoDinamico');
    $this->AddCampo('ativo'        ,'boolean',true ,'',false,false);

}

function recuperaAtributo(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
        return $this->executaRecupera("montaRecuperaAtributo",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaAtributo()
{
    $stSql = "
        SELECT  nom_atributo
             ,  valor
          FROM  licitacao.documentos_atributos
    INNER JOIN  administracao.atributo_dinamico
            ON  atributo_dinamico.cod_modulo = documentos_atributos.cod_modulo
           AND  atributo_dinamico.cod_cadastro = documentos_atributos.cod_cadastro
           AND  atributo_dinamico.cod_atributo = documentos_atributos.cod_atributo
     LEFT JOIN  licitacao.documento_atributo_valor
            ON  documento_atributo_valor.cod_documento = documentos_atributos.cod_documento
           AND  documento_atributo_valor.cod_atributo = documentos_atributos.cod_atributo
           AND  documento_atributo_valor.cod_cadastro = documentos_atributos.cod_cadastro
           AND  documento_atributo_valor.cod_modulo = documentos_atributos.cod_modulo
         WHERE  documentos_atributos.cod_documento = ".$this->getDado('cod_documento')."
    ";

    return $stSql;
}

}

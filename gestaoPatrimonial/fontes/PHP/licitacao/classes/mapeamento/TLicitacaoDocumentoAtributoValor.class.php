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
    * Classe de mapeamento da tabela licitacao.atributos_valor
    * Data de Criação: 15/09/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Nome do Programador

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 18260 $
    $Name$
    $Author: hboaventura $
    $Date: 2006-11-28 08:20:45 -0200 (Ter, 28 Nov 2006) $

    * Casos de uso: uc-03.05.12
*/
/*
$Log$
Revision 1.5  2006/11/28 10:20:45  hboaventura
Bug #7634#

Alterado o mapeamento

Revision 1.4  2006/11/08 10:51:42  larocca
Inclusão dos Casos de Uso

Revision 1.3  2006/10/03 15:15:58  tonismar
ManterCertificação

Revision 1.2  2006/09/29 16:54:10  tonismar
Arrumado nome da classe de acordo com o nome da tabela no banco

Revision 1.1  2006/09/28 09:35:04  leandro.zis
ajustes conforme alteração da tabela de documentos_atributos

Revision 1.1  2006/09/15 12:05:59  cleisson
inclusão

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
//include_once    ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  licitacao.atributos_valor
  * Data de Criação: 15/09/2006

  * @author Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Nome do Programador

  * @package URBEM
  * @subpackage Mapeamento
*/
class TLicitacaoDocumentoAtributoValor extends PersistenteAtributosValores
{
/**
    * Método Construtor
    * @access Private
*/
function TLicitacaoDocumentoAtributoValor()
{
    parent::PersistenteAtributosValores();
    $this->setTabela("licitacao.documento_atributo_valor");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_documento,cod_atributo,cod_modulo,cod_cadastro');

    $this->AddCampo('cod_documento'   ,'integer',false ,''   ,true,'TLicitacaoDocumentosAtributos');
    $this->AddCampo('cod_atributo'    ,'integer',false ,''   ,true,'TLicitacaoDocumentosAtributos');
    $this->AddCampo('cod_modulo'      ,'integer',false ,''   ,true,'TLicitacaoDocumentosAtributos');
    $this->AddCampo('cod_cadastro'    ,'integer',false ,''   ,true,'TLicitacaoDocumentosAtributos');
    $this->AddCampo('valor'           ,'varchar',false ,'40' ,false,false);

}
}

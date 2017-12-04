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
  * Classe de mapeamento da tabela DOCUMENTODINAMICO.DOCUMENTO
  * Data de Criação: 08/04/2005

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Vandré Miguel Ramos

  * @package URBEM
  * @subpackage Mapeamento

Casos de uso: uc-01.01.00

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  DOCUMENTODINAMICO.DOCUMENTO
  * Data de Criação: 08/04/2005

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Vandré Miguel Ramos

  * @package URBEM
  * @subpackage Mapeamento
*/
class TDocumentoDinamicoDocumento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TDocumentoDinamicoDocumento()
{
    parent::Persistente();
    $this->setTabela('documentoDinamico.documento');

    $this->setCampoCod('cod_documento');
    $this->setComplementoChave('');

    $this->AddCampo('cod_documento','integer',true,'',true,false);
    $this->AddCampo('cod_modulo','integer',true,'',false,true);
    $this->AddCampo('nom_documento','varchar',true,'80',false,false);
    $this->AddCampo('titulo','varchar',true,'80',false,false);
    $this->AddCampo('margem_esq','integer',true,'',false,false);
    $this->AddCampo('margem_dir','integer',true,'',false,false);
    $this->AddCampo('margem_sup','integer',true,'',false,false);
    $this->AddCampo('fonte','char',true,'1',false,false);
    $this->AddCampo('tamanho_fonte','integer',true,'',false,false);
//    $this->AddCampo('altura_linha','integer',true,'',false,false);
//    $this->AddCampo('comprimento_linha','integer',true,'',false,false);

}
}

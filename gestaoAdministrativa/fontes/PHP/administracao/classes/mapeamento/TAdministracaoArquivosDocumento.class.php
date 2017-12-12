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
    * Classe de mapeamento da tabela ADMINISTRACAO.MODELO_DOCUMENTO
    * Data de Criação: 22/02/2006

    * @author Analista:
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 18160 $
    $Name$
    $Author: tonismar $
    $Date: 2006-11-24 15:50:12 -0200 (Sex, 24 Nov 2006) $

    * Casos de uso: uc-01.03.100
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TAdministracaoArquivosDocumento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TAdministracaoArquivosDocumento()
{
    parent::Persistente();
    $this->setTabela('ADMINISTRACAO.ARQUIVOS_DOCUMENTO');

    $this->setCampoCod('cod_arquivo');
    $this->setComplementoChave('sistema');

    $this->AddCampo('cod_arquivo'       , 'integer' , true, ''      , true,  true  );
    $this->AddCampo('nome_arquivo_template'  , 'varchar' , true, '100'  , false, false );
    $this->AddCampo('checksum'          , 'text'    , true, ''  , false, false );
    $this->AddCampo('sistema'           , 'boolean' , true, ''  , false, false , "false");
}
} // end of class

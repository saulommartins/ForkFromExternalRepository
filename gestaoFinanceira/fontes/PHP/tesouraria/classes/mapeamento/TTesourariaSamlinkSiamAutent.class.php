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
    * Classe de mapeamento da tabela AUTENT do SIAM
    * Data de Criação: 10/03/2005

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.02.17
*/

/*
$Log$
Revision 1.5  2006/07/05 20:38:38  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE_SIAM );

/**
  * Efetua conexão com a tabela AUTENT do SIAM
  * Data de Criação: 10/03/2005

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TTesourariaSamlinkSiamAutent extends PersistenteSIAM
{
/**
    * Método Construtor
    * @access Private
*/
function TTesourariaSamlinkSiamAutent()
{
    parent::Persistente();
    $this->setTabela( "autent" );

    $this->setCampoCod('');
    $this->setComplementoChave('');

    $this->AddCampo('k12_conta'   , 'integer'  , 'true', '', false, false );
    $this->AddCampo('k12_valor'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('k12_empen'   , 'character', 'true', '8', false, false );
    $this->AddCampo('k12_numpre'  , 'character', 'true', '13', false, false );
    $this->AddCampo('k12_data'    , 'date'     , 'true', '', false, false );
    $this->AddCampo('k12_hora'    , 'character', 'true', '5', false, false );
    $this->AddCampo('k12_nome'    , 'character', 'true', '30', false, false );
    $this->AddCampo('k12_cheque'  , 'integer'  , 'true', '', false, false );
    $this->AddCampo('k12_compen'  , 'boolean'  , 'true', '', false, false );
    $this->AddCampo('k12_term'    , 'character', 'true', '15', false, false );
    $this->AddCampo('k12_rec01'   , 'character', 'true', '3', false, false );
    $this->AddCampo('k12_rec02'   , 'character', 'true', '3', false, false );
    $this->AddCampo('k12_rec03'   , 'character', 'true', '3', false, false );
    $this->AddCampo('k12_rec04'   , 'character', 'true', '3', false, false );
    $this->AddCampo('k12_rec05'   , 'character', 'true', '3', false, false );
    $this->AddCampo('k12_rec06'   , 'character', 'true', '3', false, false );
    $this->AddCampo('k12_rec07'   , 'character', 'true', '3', false, false );
    $this->AddCampo('k12_rec08'   , 'character', 'true', '3', false, false );
    $this->AddCampo('k12_rec09'   , 'character', 'true', '3', false, false );
    $this->AddCampo('k12_rec10'   , 'character', 'true', '3', false, false );
    $this->AddCampo('k12_rec11'   , 'character', 'true', '3', false, false );
    $this->AddCampo('k12_rec12'   , 'character', 'true', '3', false, false );
    $this->AddCampo('k12_rec13'   , 'character', 'true', '3', false, false );
    $this->AddCampo('k12_rec14'   , 'character', 'true', '3', false, false );
    $this->AddCampo('k12_rec15'   , 'character', 'true', '3', false, false );
    $this->AddCampo('k12_rec16'   , 'character', 'true', '3', false, false );
    $this->AddCampo('k12_rec17'   , 'character', 'true', '3', false, false );
    $this->AddCampo('k12_rec18'   , 'character', 'true', '3', false, false );
    $this->AddCampo('k12_rec19'   , 'character', 'true', '3', false, false );
    $this->AddCampo('k12_rec20'   , 'character', 'true', '3', false, false );
    $this->AddCampo('k12_vlr01'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('k12_vlr02'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('k12_vlr03'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('k12_vlr04'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('k12_vlr05'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('k12_vlr06'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('k12_vlr07'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('k12_vlr08'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('k12_vlr09'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('k12_vlr10'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('k12_vlr11'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('k12_vlr12'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('k12_vlr13'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('k12_vlr14'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('k12_vlr15'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('k12_vlr16'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('k12_vlr17'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('k12_vlr18'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('k12_vlr19'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('k12_vlr20'   , 'numeric'  , 'true', '', false, false );
    $this->AddCampo('k12_estorn'  , 'boolean'  , 'true', '', false, false );
    $this->AddCampo('k12_autent'  , 'integer'  , 'true', '', false, false );
    $this->AddCampo('k12_entidade', 'integer'  , 'true', '3', false, false );

}

}

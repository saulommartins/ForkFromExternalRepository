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
    * Classe de mapeamento da tabela AUTORIZA do SIAM
    * Data de Criação: 26/05/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.03.15
*/

/*
$Log$
Revision 1.4  2006/07/05 20:46:56  cleisson
Adicionada tag Log aos arquivos

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE_SIAM );

/**
  * Efetua conexão com a tabela AUTORIZA do SIAM
  * Data de Criação: 26/05/2005

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Anderson R. M. Buzo

  * @package URBEM
  * @subpackage Mapeamento

*/
class TSamlinkSiamAutoriza extends PersistenteSIAM
{
/**
    * Método Construtor
    * @access Private
*/
function TSamlinkSiamAutoriza()
{
    parent::Persistente();
    $this->setTabela( "AUTORIZA" );

    $this->setCampoCod('e04_autori');
    $this->setComplementoChave('');

    $this->AddCampo('e04_autori', 'char'   , true , '8' , true , false );
    $this->AddCampo('e04_destin', 'char'   , false, '35', false, false );
    $this->AddCampo('e04_valor' , 'double' , false, ''  , false, false );
    $this->AddCampo('e04_dotac' , 'integer', false, ''  , false, false );
    $this->AddCampo('e04_numcgm', 'integer', false, ''  , false, false );
    $this->AddCampo('e04_tipol' , 'char'   , false, '1' , false, false );
    $this->AddCampo('e04_numerl', 'char'   , false, '8' , false, false );
    $this->AddCampo('e04_praent', 'char'   , false, '30', false, false );
    $this->AddCampo('e04_entpar', 'char'   , false, '30', false, false );
    $this->AddCampo('e04_conpag', 'char'   , false, '30', false, false );
    $this->AddCampo('e04_codout', 'char'   , false, '30', false, false );
    $this->AddCampo('e04_contat', 'char'   , false, '20', false, false );
    $this->AddCampo('e04_telef' , 'char'   , false, '20', false, false );
    $this->AddCampo('e04_numsol', 'integer', false, ''  , false, false );
    $this->AddCampo('e04_anulad', 'date'   , false, ''  , false, false );
    $this->AddCampo('e04_emiss' , 'date'   , false, ''  , false, false );
    $this->AddCampo('e04_login' , 'char'   , false, '8' , false, false );
    $this->AddCampo('e04_resumo', 'text'   , false, ''  , false, false );
    $this->AddCampo('e04_empenh', 'char'   , false, '8' , false, false );
    $this->AddCampo('e04_elemen', 'char'   , false, '12', false, false );
}

}

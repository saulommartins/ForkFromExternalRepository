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
    * Classe de mapeamento da tabela TABREC do SIAM
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
include_once ( CLA_PERSISTENTE );
/**
  * Classe de mapeamento da tabela TABREC do SIAM
  * Data de Criação: 10/03/2005

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
*/

class TTesourariaSamlinkSiamTabrec extends PersistenteSIAM
{
/**
    * Método Construtor
    * @access Private
*/
function TTesourariaSamlinkSiamTabrec()
{
    parent::Persistente();
    $this->setTabela( "TABREC" );

    $this->setCampoCod('');
    $this->setComplementoChave('');

    $this->AddCampo( 'k01_anoexe', 'integer', true,'',false,false );
    $this->AddCampo( 'k01_codigo', 'bpchar' , true,'3',false,false );
    $this->AddCampo( 'k01_tipo', 'bpchar'   , true,'1',false,false );
    $this->AddCampo( 'k01_conta', 'integer' , true,'',false,false );
    $this->AddCampo( 'k01_descr', 'bpchar'  , true,'13',false,false );
    $this->AddCampo( 'k01_drecei', 'bpchar' , true,'40',false,false );
    $this->AddCampo( 'k01_corr', 'bpchar'   , true,'5',false,false );
    $this->AddCampo( 'k01_juros', 'numeric' , true,'',false,false );
    $this->AddCampo( 'k01_mult01', 'integer', true,'',false,false );
    $this->AddCampo( 'k01_mult02', 'integer', true,'',false,false );
    $this->AddCampo( 'k01_mult03', 'integer', true,'',false,false );
    $this->AddCampo( 'k01_faix01', 'numeric', true,'',false,false );
    $this->AddCampo( 'k01_faix02', 'numeric', true,'',false,false );
    $this->AddCampo( 'k01_faix03', 'numeric', true,'',false,false );
    $this->AddCampo( 'k01_desco1', 'numeric', true,'',false,false );
    $this->AddCampo( 'k01_desco2', 'numeric', true,'',false,false );
    $this->AddCampo( 'k01_desco3', 'numeric', true,'',false,false );
    $this->AddCampo( 'k01_desco4', 'numeric', true,'',false,false );
    $this->AddCampo( 'k01_desco5', 'numeric', true,'',false,false );
    $this->AddCampo( 'k01_desco6', 'numeric', true,'',false,false );
    $this->AddCampo( 'k01_dtdes1', 'date'   , true,'',false,false );
    $this->AddCampo( 'k01_dtdes2', 'date'   , true,'',false,false );
    $this->AddCampo( 'k01_dtdes3', 'date'   , true,'',false,false );
    $this->AddCampo( 'k01_dtdes4', 'date'   , true,'',false,false );
    $this->AddCampo( 'k01_dtdes5', 'date'   , true,'',false,false );
    $this->AddCampo( 'k01_dtdes6', 'date'   , true,'',false,false );
    $this->AddCampo( 'k01_mult04', 'integer', true,'',false,false );
    $this->AddCampo( 'k01_mult05', 'integer', true,'',false,false );
    $this->AddCampo( 'k01_mult06', 'integer', true,'',false,false );
    $this->AddCampo( 'k01_faix04', 'numeric', true,'',false,false );
    $this->AddCampo( 'k01_faix05', 'numeric', true,'',false,false );
    $this->AddCampo( 'k01_faix06', 'numeric', true,'',false,false );
    $this->AddCampo( 'k01_integr', 'boolean', true,'',false,false );
    $this->AddCampo( 'k01_dtfrac', 'date'   , true,'',false,false );
    $this->AddCampo( 'k01_mulfra', 'numeric', true,'',false,false );
    $this->AddCampo( 'k01_limmul', 'numeric', true,'',false,false );
    $this->AddCampo( 'k01_jurpar', 'numeric', true,'',false,false );
    $this->AddCampo( 'k01_desjm', 'boolean' , true,'',false,false );
    $this->AddCampo( 'k01_caldes', 'boolean', true,'',false,false );
    $this->AddCampo( 'k01_jurdia', 'boolean', true,'',false,false );
    $this->AddCampo( 'k01_juracu', 'boolean', true,'',false,false );
    $this->AddCampo( 'k01_corven', 'boolean', true,'',false,false );

}
}

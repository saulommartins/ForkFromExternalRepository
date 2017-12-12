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
* Arquivo de popup
* Data de Criação: 23/06/2006

* @author Analista: Diego Barbosa Victoria
* @author Desenvolvedor: Diego Barbosa Victoria

* @package URBEM
* @subpackage

*/

include_once(CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php");

class  IPopUpPosto extends IPopUpCGMVinculado
{
/**
    * Metodo Construtor
    * @access Public

*/
function IPopUpPosto(&$obForm)
{
    parent::IPopUpCGMVinculado($obForm);

    $this->setRotulo                 ( 'Posto'        );
    $this->setTitle                  ( 'Informe o posto.'  );

    $this->setId                     ('stNomCGMPosto');
    $this->obCampoCod->setId         ( "inCGMPosto"    );
    $this->obCampoCod->setName       ( "inCGMPosto"    );
    $this->obCampoCod->setSize       ( 10              );
    $this->obCampoCod->setMaxLength  ( 10              );
    $this->obCampoCod->setAlign      ( "left"          );

    $this->setTabelaVinculo          ( 'frota.posto' );
    $this->setCampoVinculo           ( 'cgm_posto'               );
    $this->setNomeVinculo            ( 'Posto'          );

    $this->stTipo = 'vinculado';
}

    public function setSomenteAtivos($valor) { $this->boSomenteAtivos = $valor; }
    public function getSomenteAtivos() { return $this->boSomenteAtivos; }

    public function setSomenteInternos($valor) { $this->boSomenteInternos = $valor; }
    public function getSomenteInternos() { return  $this->boSomenteInternos; }

    public function montaHTML()
    {
        $stFiltroVinculado = "";
        if ($this->boSomenteAtivos) {
             $stFiltroVinculado .= " AND  tabela_vinculo.ativo = true";
        }
        if ($this->boSomenteInternos) {
             $stFiltroVinculado .= " AND  tabela_vinculo.interno = true";
        }
        if ($stFiltroVinculado) {
             $this->setFiltroVinculado( $stFiltroVinculado );
        }
        parent::montaHTML();
    }

}
?>

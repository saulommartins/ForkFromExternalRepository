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

    * Arquivo que monta inner de busca licenca
    * Data de Criação: 08/09/2014

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Carolina Schwaab Marçal

    * @package URBEM
    * @subpackage

    * $Id: ITextLicenca.class.php 59845 2014-09-15 19:32:00Z carolina $

    * Casos de uso: uc-05.02.12

*/

include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMLicenca.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );


class ITextLicenca extends Objeto
{
    public $boNull;
    public $inCodLicenca;
    public $inExercicio;
    public $stMascara;
    public $stTipoLicenca;
    public $obTxtLicenca;
    public $obHdnTipoLicenca;

    public function setTipoLicenca($valor) { $this->stTipoLicenca = $valor; }
    public function getTipoLicenca() { return $this->stTipoLicenca; }
    public function setNull($valor) { $this->boNull = $valor; }
    public function getNull() { return $this->boNull; }

    public function ITextLicenca()
    {
           

        $this->obHdnTipoLicenca = new Hidden;
        $this->obHdnTipoLicenca->setName ('stTipoLicenca');
        $this->obHdnTipoLicenca->setName ( $this->getTipoLicenca() );

        $this->inExercicio = Sessao::getExercicio();
        $this->obTxtLicenca = new TextBox;
        #$this->obTxtLicenca->stMascara = "9/9999";

        $obTConfiguracao = new TAdministracaoConfiguracao;
        $obTConfiguracao->setDado ( 'cod_modulo', 14 );
        $obTConfiguracao->setDado ( 'parametro', 'numero_licenca' );
        $obTConfiguracao->setDado ( 'exercicio', Sessao::getExercicio() );
        $obTConfiguracao->recuperaPorChave ( $rsNumeroLicenca );
        $inNumeroLicenca = $rsNumeroLicenca->getCampo('valor');
      

        $obTConfiguracao->setDado ( 'parametro', 'mascara_licenca' );
        $obTConfiguracao->recuperaPorChave ( $rsMascaraLicenca );
        $contNumLicenca = strlen ( $rsMascaraLicenca->getCampo('valor') );
        $i = 0;
        $this->obTxtLicenca->stMascara = null;
        while ($i < $contNumLicenca) {
            $this->obTxtLicenca->stMascara .= "9";
            $i++;
        }

        if ( $contNumLicenca <= 0 )
            $this->obTxtLicenca->stMascara .= "9";

        if ( $inNumeroLicenca != 0 )
            $this->obTxtLicenca->stMascara .= '/9999';

        $inNumeroCaracteres = strlen ( $this->obTxtLicenca->stMascara );

        $this->obTxtLicenca->setRotulo      ( 'Número da Licença'               );
        $this->obTxtLicenca->setTitle       ( 'Selecione o número da Licença.'  );
        $this->obTxtLicenca->setNull        ( $this->obTxtLicenca->getNull()    );

        $this->obTxtLicenca->setName        ( "stLicenca"                       );
        $this->obTxtLicenca->setSize        ( $inNumeroCaracteres 		        );
        $this->obTxtLicenca->setMaxLength   ( $inNumeroCaracteres               );
        $this->obTxtLicenca->setAlign       ( "left"                            );
        $this->obTxtLicenca->setMascara	    ( $this->obTxtLicenca->stMascara    );
        $this->obTxtLicenca->setInteiro     ( false );

   }

    public function geraFormulario(&$obFormulario)
    {
           ;

        $obFormulario->addHidden      ( $this->obHdnTipoLicenca );
        $obFormulario->addComponente  ( $this->obTxtLicenca );

    }

}
?>

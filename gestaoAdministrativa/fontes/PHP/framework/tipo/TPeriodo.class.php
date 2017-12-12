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
*
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00
*/

class TPeriodo
{
/*
    * @var Object
    * @access Private
*/
var $obTDataInicial;
/*
    * @var Object
    * @access Private
*/
var $obTDataFinal;

/*
    * @access Public
    * @param Object $valor
*/
function setTDataInicial($valor) { $this->obTDataInicial   = $valor; }
/*
    * @access Public
    * @param Object $valor
*/
function setTDataFinal($valor) { $this->obTDataFinal     = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setDataInicial($valor)
{
    $obErro = $this->obTDataInicial->setData ($valor);

    if ( !$obErro->ocorreu() ) {
        if ($this->obTDataFinal->getData()) {
            $obErro = $this->comparaDatas("Inicial");
        }
    }

    return $obErro;
}
/**
     * @access Public
     * @param String $valor
*/
function setDataFinal($valor)
{
    $obErro = $this->obTDataFinal->setData ($valor);

    if ( !$obErro->ocorreu() ) {
        if ($this->obTDataInicial->getData()) {
            $obErro = $this->comparaDatas("Final");
        }
    }

    return $obErro;
}

/*
    * @access Public
    * @return Object
*/
function getTDataInicial() { return $this->obTDataInicial;   }
/*
    * @access Public
    * @return Object
*/
function getTDataFinal() { return $this->obTDataFinal;     }
/**
     * @access Public
     * @param String $valor
*/
function getDataInicial() { return $this->obTDataInicial->getData();             }
/**
     * @access Public
     * @param String $valor
*/
function getDataFinal() { return $this->obTDataFinal->getData();               }

/**
    * Método Construtor
    * @access Private
*/
function TPeriodo()
{
    include_once( CAM_FW_TIPO               ."TData.class.php"                      );
    $this->obTDataInicial       =  new TData;
    $this->obTDataFinal         =  new TData;
}

/**
    * Valida Data Inicial e Final
    * @access Private
    * @param String Data Informada
    * @return Object Erro
*/
function comparaDatas($valor)
{
    $obErro = new Erro;

    list( $diaInicial,$mesInicial,$anoInicial ) = explode( '/', $this->obTDataInicial->getData());
    list( $diaFinal,$mesFinal,$anoFinal )       = explode( '/', $this->obTDataFinal->getData()  );

    if ( mktime ( 0, 0, 0, $mesInicial, $diaInicial, $anoInicial)  > mktime ( 0, 0, 0, $mesFinal, $diaFinal, $anoFinal) ) {
        if($valor=="Inicial")
            $obErro->setDescricao("Data Inicial deve ser menor que a Data Final");
        else
            $obErro->setDescricao("Data Final deve ser maior que a Data Inicial");
    }

    return $obErro;
}

}
